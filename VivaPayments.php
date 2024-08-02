<?php 
/*
 * VivaPayments EFT POS API Integration Class 
 * 
 * @version 1.0.0
 * @package VivaPayments
 * @author Adam Touhou
 * @license MIT
 * @link https://github.com/ATouhou/VivaPayments
 * @link https://developer.viva.com/apis-for-point-of-sale/card-terminals-devices/rest-api/eft-pos-api-documentation/
 */



class VivaPayments {
  /*
    private $environment;

  
    public function __construct($environment = 'sandbox'){
      $this->environment = $environment;
    }
    */

    public function getAuthToken($merchantId, $merchantSecret){

        $base64encoded = base64_encode($merchantId . ":" . $merchantSecret);
        $curl = curl_init();
      
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://demo-accounts.vivapayments.com/connect/token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
          CURLOPT_HTTPHEADER => array(
              'Content-Type: application/x-www-form-urlencoded',
              'Authorization: Basic ' . $base64encoded
          ),
        ));
      
        $response = curl_exec($curl);
        $response = json_decode($response, true); 
      
        curl_close($curl);
        return $response['access_token']; 
      
    }

    public function getPosDevices($authToken, $merchantId){
        $curl = curl_init();
      
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://demo-api.vivapayments.com/ecr/isv/v1/devices:search',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "merchantId": "'. $merchantId .'",
          "statusId": 1
        }',
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $authToken",
            'Cookie: ak_bmsc=D8907F045F2FDBEDA67D59C90AC319BD~000000000000000000000000000000~YAAQhZs+F4XxY2CQAQAAZdf/dxgUOo2mBdz55Dw5OxtVgLljSxa/3U31zkt7xms4ReimioSi8ZUDSzjU3b1Bv7RjjZNmvzEDsHj+kO5SKx2lXScNmIE3JeHAcFwSdMdTxftLOQTzHnj7/C8kmrNVitZ55xro5Oqd2goMYwYD0d7nbjEZ+okP//xLEnX+h+eL17waik1bmkkPFoZbobdFAZGd1ocTs5LyeTKahpttyX1fAWPLj4re3cVD7cJXpk9zJ+p0DB9UXQrPESIeaOsfWnuYGFF1ixS+XcePvj/JgTI5JylL7WA8U7qfsn7cok5T/Qjb56p3xjAHpKn2ID656iMIVH43mgTbXagsVa7m3RCowk1CnrGIrdC7owZLPHmKSA==; ASLBSA=000380b011f30db19ac7bcee32ea1b13e45a45712f55c4b7794b8d52418c417cee64eb0c966577eba02b8c8bcfea31c66f47ec942fbc12c08e9dbe4aa8bdb50eeac8; ASLBSACORS=000380b011f30db19ac7bcee32ea1b13e45a45712f55c4b7794b8d52418c417cee64eb0c966577eba02b8c8bcfea31c66f47ec942fbc12c08e9dbe4aa8bdb50eeac8'
          ),
        )); 
        
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);
        return $response;
    }

    public function randomSessionId(){
        return bin2hex(random_bytes(16));
    }

    public function initiatePayment($authToken, $amount, $terminalId, $merchantId, $sourceCode, $vivaorderreference){
        $curl = curl_init();
        $randomsessionid = $this->randomSessionId();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://demo-api.vivapayments.com/ecr/isv/v1/transactions:sale',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
          "sessionId": "'. $randomsessionid .'",
          "terminalId": "'. $terminalId .'",
          "cashRegisterId": "cashRegisterId123",
          "amount": '.$amount.',
          "currencyCode": "208",
          "merchantReference": "Mad&Drikke",
          "customerTrns": "'.$vivaorderreference .'",
          "tipAmount": 0,
          "isvDetails": {
              "amount": 122,
              "merchantId": "'. $merchantId .'",
              "sourceCode": "'. $sourceCode .'",
              "terminalMerchantId": "'. $merchantId .'"
            }
          }',
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $authToken",
            "Cookie: ak_bmsc=D8907F045F2FDBEDA67D59C90AC319BD~000000000000000000000000000000~YAAQhZs+F4XxY2CQAQAAZdf/dxgUOo2mBdz55Dw5OxtVgLljSxa/3U31zkt7xms4ReimioSi8ZUDSzjU3b1Bv7RjjZNmvzEDsHj+kO5SKx2lXScNmIE3JeHAcFwSdMdTxftLOQTzHnj7/C8kmrNVitZ55xro5Oqd2goMYwYD0d7nbjEZ+okP//xLEnX+h+eL17waik1bmkkPFoZbobdFAZGd1ocTs5LyeTKahpttyX1fAWPLj4re3cVD7cJXpk9zJ+p0DB9UXQrPESIeaOsfWnuYGFF1ixS+XcePvj/JgTI5JylL7WA8U7qfsn7cok5T/Qjb56p3xjAHpKn2ID656iMIVH43mgTbXagsVa7m3RCowk1CnrGIrdC7owZLPHmKSA==; ASLBSA=000380b011f30db19ac7bcee32ea1b13e45a45712f55c4b7794b8d52418c417cee64eb0c966577eba02b8c8bcfea31c66f47ec942fbc12c08e9dbe4aa8bdb50eeac8; ASLBSACORS=000380b011f30db19ac7bcee32ea1b13e45a45712f55c4b7794b8d52418c417cee64eb0c966577eba02b8c8bcfea31c66f47ec942fbc12c08e9dbe4aa8bdb50eeac8"
          ),
        ));
        // https://developer.viva.com/apis-for-point-of-sale/card-terminals-devices/rest-api/eft-pos-api-documentation/#tag/Make-Transactions/paths/~1ecr~1isv~1v1~1transactions:sale/post 

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($response, true);
        return $response;
    }

    public function getUniqueVerificationCode($merchantId, $apiKey){
        $base64encoded = base64_encode($merchantId . ":" . $apiKey);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://demo.vivapayments.com/api/messages/config/token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            "Authorization: Basic $base64encoded",
            "Cookie: ak_bmsc=ECD51B76A9A795962212425040D09971~000000000000000000000000000000~YAAQT2QwFyQYDnCQAQAAddm6eBi+CHRKD8Ff84BpFF4esZuO8LasjHzz1PWnbdDuNV/dv4AV+tfLxnFxtJGM4pOB8CVf1TryijN6CU9FXvOxokdJcsBQ7WlGJ67p/Ku69D6care2QNZgtHvdieudPTvT7rLMny35fBoC5yGxRYtcdQQwLoR0nNwh/OteYhWnMPqRoUvbKo3ufuYiLut6M7SSowVnah4ab44gRnRfy4R/07edxO9hy4YSy6ZabKUHkPQM29PKuWINfy60QysOqLEIf1FDYfxX6OuFG1qe7rKp2jeXjWFBppPCSMn/YRbumlPDFH8W6aWu3VgzHbo1Zj1zPNy15Cdv6dsBVfuSajSWpzwTeHOz/jY51cMUT0/7lQ=="
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        json_decode($response, true);
        return $response;
    }


                  
  } // end class
  