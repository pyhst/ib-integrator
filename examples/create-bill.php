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

// try {
	$bill = $vendor->CreateBilling($transaction);
// } catch (\Throwable $e) {
// 	echo ErrorString($e);
// }
