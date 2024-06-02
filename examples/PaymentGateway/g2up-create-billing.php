<?php

use IbIntegrator\Vendors\PaymentGateway\G2UP;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';

$vendor = new G2UP();
$vendor->setID($_ENV['G2UP_APP_KEY']);
$vendor->setSecret($_ENV['G2UP_SECRET_KEY']);
$vendor->setHostURL($_ENV['G2UP_HOST_URL']);
$vendor->setPaymentURL($_ENV['G2UP_HOST_URL']);
$vendor->setRequestURL($_ENV['G2UP_HOST_URL']);
$vendor->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$vendor->setParams([
	//
]);

/**
 *
 * Create billing
 *
 */
try {
	$transaction = new Transaction();
	$transaction->setAmount(15000);
	$transaction->setReferenceNumber(time());
	$transaction->setDescription('BILLING1');
	//
	/* // Channel list:
		BNI_VA
		CIMB_VA
		Mandiri_VA
		Permata_VA
		BCA_VA
		BRI_VA
		MBayar_QR / QRIS
	*/
	// $transaction->setPaymentChannel('CIMB_VA');
	// $transaction->setPaymentChannel('BCA_VA');
	$transaction->setPaymentChannel('MBayar_QR');
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	/* // Success VA
		{
			"status": "000",
			"data": {
				"status": true,
				"error_code": "P00",
				"error_desc": "Data format is good",
				"ref_id": "20240423154504-6627751009B73",
				"request_id": "1713861903",
				"channel": "cimb_va",
				"amount": "15000",
				"admin_fee": "4900",
				"total_amount": "19900",
				"virtual_account": "9999900000000006",
				"qrcode": null
			}
		}
	*/
	/* // Success QRIS
		{
			"status": "000",
			"data": {
				"status": true,
				"error_code": "P00",
				"error_desc": "Data format is good",
				"ref_id": "20240423162310-66277DFE23CDA",
				"request_id": "1713864189",
				"channel": "MBayar_QR",
				"amount": "15000",
				"admin_fee": "117",
				"total_amount": "15117",
				"virtual_account": null,
				"qrcode": "https:\/\/sandbox-api.gempay.online\/v1\/qrcode?UzRNUDFsRWRUTldXTVhjbmVZdU1qWSs0blhrYyswSzRKMU5HK1p6ZU9aMD0="
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}