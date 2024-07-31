# VivaPayments Integration

This repository contains a PHP class for integrating with the VivaPayments API (EFT POS API). The `VivaPayments` class provides methods to handle various payment-related operations. 

## Features
- Generate necessary authentication token
- Get list of available POS devices
- Generate random session IDs
- Initiate payments
- Handle API requests (getUniqueVerificationCode)

- Refund capability will be added shortly. 
- Perhaps also the functionality to add the global ISV webhooks will be added.

## Requirements

- PHP 7.4 or higher
- cURL extension enabled

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/ATouhou/VivaPayments.git
    ```
2. Include the `VivaPayments.php` file in your project:
    ```php
    require_once 'path/to/VivaPayments.php';
    ```

## Configuration

Before using the `VivaPayments` class, you need to set up your API credentials. 
I do not add  `merchant_id`, `api_key` to the constructer as there is a difference between merchant and ISV setup. This allows flexability and multiple credentials to be used in the same instigation of the class. 
URL is hardcoded to demo setup. For production change the code in the class. At some point I may add the possibility for choosing between development/production upon loading the class. 
Production URL = "https://accounts.vivapayments.com/";
Development URL = "https://demo-accounts.vivapayments.com/";


## Usage

### Instantiate the Class

Create an instance of the `VivaPayments` class by passing the required parameters to the constructor.

```php
<?php
require_once 'VivaPayments.php';

$VivaPayment = new VivaPayments();

$merchantId = "...";
$apiKey = '...';
$merchantClientId = "....apps.vivapayments.com"; 
$merchantClientSecret = "."; 
$isvClientId = "...apps.vivapayments.com";
$isvClientSecret = "...";
$terminalId = "160....";
$sourceCode = '6...';
$amount = 1000;

$authToken = $vivaPayment->getAuthToken($merchantClientId, $merchantClientSecret);

echo "standard Token <pre>";
print_r($authToken);
echo "</pre>";

$authToken2 = $vivaPayment->getAuthToken($isvClientId, $isvClientSecret);

echo "ISV token <pre>";
print_r($authToken2);
echo "</pre>";

$posDevices = $vivaPayment->getPosDevices($authToken2, $merchantId);
echo "POS Devices <pre>";
print_r($posDevices);
echo "</pre>";


echo "Initiate Payment <pre>";
$vivaPayment->initiatePayment($authToken2, $amount, $terminalId, $merchantId, $sourceCode);
echo "</pre>";


$verificationcode = $vivaPayment->getUniqueVerificationCode($merchantId, $apiKey);
echo "Verification Code <pre>";
print_r($verificationcode);
echo "</pre>";

```


Handle API Requests
You can handle various API requests using the methods provided in the VivaPayments class. Ensure that you check the API documentation for the required parameters and request formats.

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request for any improvements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Support
If you encounter any issues or have questions, feel free to open an issue on GitHub.


