<?php

use IbIntegrator\Vendors\PaymentGateway\AyoConnect;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';



$vendor = new AyoConnect;
$vendor->setMerchantCode($_ENV['AYOCONNECT_MERCHANT_CODE']);
$vendor->setID($_ENV['AYOCONNECT_CLIENT_ID']);
$vendor->setSecret($_ENV['AYOCONNECT_CLIENT_SECRET']);
$vendor->setHostURL($_ENV['AYOCONNECT_HOST_URL']);



try {
	$result = $vendor->AuthGetToken();
	extract($result);
	print_r($response);
	/* Success token
		{
		"status": "000",
		"data": {
			"apiProductList": "[of-oauth, bank-account-disbursement-sandbox]",
			"organizationName": "ayoconnect-open-finance",
			"developer.email": "sean@nusi.co.id",
			"tokenType": "BearerToken",
			"responseTime": "20231013035033",
			"clientId": "tBPrfKAluJkesLVvevUCyagPgTmtKA20HX9uleuLof4u1A8b",
			"accessToken": "IQSSsIgdrEsQeFHtEI5u37ZgFEBA",
			"expiresIn": "3599"
		}
	}
	*/
	$vendor->setToken(json_decode($response['content'])->data->accessToken);
} catch (\Throwable $e) {
	echo ErrorString($e);
}



// try {
// 	$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
// 	$rand = substr(str_shuffle($permitted_chars), 0, 22);
// 	$transaction = new Transaction;
// 	$transaction->setOrderID(time() . $rand);
// 	$result = $vendor->CheckAccountBalance($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* Success
// 		{
// 			"status": "000",
// 			"data": [
// 				{
// 						"availableBalance": {
// 							"value": "0.00",
// 							"currency": "IDR"
// 						}
// 				}
// 			]
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorString($e);
// }



try {
	$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$rand = substr(str_shuffle($permitted_chars), 0, 22);
	$transaction = new Transaction;
	$transaction->setOrderID(time() . $rand);
	$transaction->setCustomerPhone('6281212121314');
	$transaction->setCustomerBankCode('GNESIDJA');
	$transaction->setCustomerBankAccountNumber('510654300');
	$transaction->setIPAddress('192.168.100.12');
	$result = $vendor->BankAccountInquiry($transaction);
	extract($result);
	print_r($response);
	/* Success
		{
			"status": "000",
			"data": [
				{
						"availableBalance": {
							"value": "0.00",
							"currency": "IDR"
						}
				}
			]
		}
	*/
} catch (\Throwable $e) {
	echo ErrorString($e);
}



// try {
// 	$transaction = new Transaction;
// 	$transaction->setOrderID(time());
// 	$transaction->setAmount(100000);
// 	$transaction->setCustomerName('TESTER');
// 	$transaction->setPaymentMethod('BC');
// 	$result = $vendor->CreateBilling($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* Success VA
// 		{
// 			"status": "000",
// 			"data": {
// 				"merchantCode": "DS15995",
// 				"reference": "DS15995232BRQW0LSNIZ2X0R",
// 				"paymentUrl": "https:\/\/sandbox.duitku.com\/topup\/topupdirectv2.aspx?ref=BC23KCKTTVV4GYURR1L",
// 				"vaNumber": "7007014006445903",
// 				"amount": "100000",
// 				"statusCode": "00",
// 				"statusMessage": "SUCCESS"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorString($e);
// }



// try {
// 	$inquiry = new Transaction;
// 	$inquiry->setOrderID('7007014006445903');
// 	$result = $vendor->InquiryPayment($inquiry);
// 	extract($result);
// 	print_r($response);
// 	/* Success
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorString($e);
// }



// try {
// 	$callback = '
// 		{
// 			"merchantCode": "D6677",
// 			"amount": "100000",
// 			"merchantOrderId": "0001285662",
// 			"productDetail": "Payment for order 0001285662",
// 			"additionalParam": null,
// 			"resultCode": "00",
// 			"signature": "439030a6da086ee13558137f07d4a27d",
// 			"paymentCode": "VC",
// 			"merchantUserId": null,
// 			"reference": "D6677JXVYL752HMAV0AD"
// 		}';
// 	$callback = json_decode($callback);
// 	$result = $vendor->PaymentCallback($callback);
// 	extract($result);
// 	print_r($response);
// 	/* Success
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorString($e);
// }



// try {
// 	$banklist = new Transaction;
// 	$banklist = $vendor->GetBankList($banklist);
// 	extract($banklist);
// 	print_r($response);
// 	/* Success
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorString($e);
// }


