<?php

use IbIntegrator\Vendors\PaymentGateway\Duitku;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/includes.php';

$vendor = new Duitku;
$vendor->setID($_ENV['DUITKU_MERCHANT_ID']);
$vendor->setAPIKey($_ENV['DUITKU_API_KEY']);
$vendor->setHostURL($_ENV['DUITKU_HOST_URL']);
$vendor->setParams([
	'DisbursementMerchantID' => $_ENV['DUITKU_API_KEY'],
	'DisbursementAPIKey' => $_ENV['DUITKU_DISBURSEMENT_API_KEY'],
	'DisbursementEmail' => $_ENV['DUITKU_DISBURSEMENT_EMAIL'],
]);

$transaction = new Transaction;
$transaction->setOrderID(time());
$transaction->setAmount(100000);
$transaction->setCustomerName('TESTER');
$transaction->setPaymentMethod('BC');

try {
	$result = $vendor->CreateBilling($transaction);
	extract($result);
	print_r($response);
	/* Success VA
		{
			"status": "000",
			"data": {
				"merchantCode": "DS15995",
				"reference": "DS15995232BRQW0LSNIZ2X0R",
				"paymentUrl": "https:\/\/sandbox.duitku.com\/topup\/topupdirectv2.aspx?ref=BC23KCKTTVV4GYURR1L",
				"vaNumber": "7007014006445903",
				"amount": "100000",
				"statusCode": "00",
				"statusMessage": "SUCCESS"
			}
		}
	*/
} catch (\Throwable $e) {
	echo ErrorString($e);
}

