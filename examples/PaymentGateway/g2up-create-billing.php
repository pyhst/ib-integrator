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
$vendor->setCallbackURL('http://tester.com/secure/callback/demo');
$vendor->setParams([
	//
]);



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new Transaction();
// 	$transaction->setCustomerID('1234567890');
// 	$transaction->setAmount(15000);
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setDescription('BILLING1');
// 	//
// 	/* // Channel list:
// 		BNI_VA
// 		CIMB_VA
// 		Mandiri_VA
// 		Permata_VA
// 		BCA_VA
// 		BRI_VA
// 		MBayar_QR / QRIS
// 	*/
// 	// $transaction->setPaymentChannel('CIMB_VA');
// 	// $transaction->setPaymentChannel('BCA_VA');
// 	$transaction->setPaymentChannel('MBayar_QR');
// 	$result = $vendor->CreateBilling($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success VA
// 		{
// 			"status": "000",
// 			"data": {
// 				"status": true,
// 				"error_code": "P00",
// 				"error_desc": "Data format is good",
// 				"ref_id": "20240423154504-6627751009B73",
// 				"request_id": "1713861903",
// 				"channel": "cimb_va",
// 				"amount": "15000",
// 				"admin_fee": "4900",
// 				"total_amount": "19900",
// 				"virtual_account": "9999900000000006",
// 				"qrcode": null
// 			}
// 		}
// 	*/
// 	/* // Success QRIS
// 		{
// 			"status": "000",
// 			"data": {
// 				"status": true,
// 				"error_code": "P00",
// 				"error_desc": "Data format is good",
// 				"ref_id": "20240423162310-66277DFE23CDA",
// 				"request_id": "1713864189",
// 				"channel": "MBayar_QR",
// 				"amount": "15000",
// 				"admin_fee": "117",
// 				"total_amount": "15117",
// 				"virtual_account": null,
// 				"qrcode": "https:\/\/sandbox-api.gempay.online\/v1\/qrcode?UzRNUDFsRWRUTldXTVhjbmVZdU1qWSs0blhrYyswSzRKMU5HK1p6ZU9aMD0="
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Inquiry trx
//  *
//  */
// try {
// 	$transaction = new Transaction();
// 	$transaction->setStartDate('2024-05-21');
// 	$transaction->setEndDate('2024-05-22');
// 	// $transaction->setReferenceNumber('1716267137');
// 	// $transaction->setCustomerID('1234567890');
// 	$result = $vendor->InquiryPayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"data": [
// 				{
// 						"referenceNo": "1716267137",
// 						"memberNo": "TESTER",
// 						"topUpAmount": 15000,
// 						"requestDate": "2024-05-21 11:52:20",
// 						"responseDate": "2024-05-21 11:52:21",
// 						"paymentNo": null,
// 						"paymentDate": null,
// 						"paymentStatus": null
// 				}
// 			]
// 		}
// 	*/
// 	/* // Success inquiry of paid QRIS
// 		{
// 			"status": "000",
// 			"data": [
// 				{
// 						"referenceNo": "1716267137",
// 						"memberNo": "TESTER",
// 						"topUpAmount": 15000,
// 						"requestDate": "2024-05-21 11:52:20",
// 						"responseDate": "2024-05-21 11:52:21",
// 						"paymentNo": "QRP24052100001",
// 						"paymentDate": "2024-05-21 11:56:54",
// 						"paymentStatus": "00"
// 				}
// 			]
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // vendor->ThrowErrorException($e);
// }
