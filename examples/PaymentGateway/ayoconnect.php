<?php

use IbIntegrator\Vendors\PaymentGateway\AyoConnect;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';



$vendor = new AyoConnect;
$vendor->setMerchantCode($_ENV['AYOCONNECT_MERCHANT_CODE']);
$vendor->setID($_ENV['AYOCONNECT_CLIENT_ID']);
$vendor->setSecret($_ENV['AYOCONNECT_CLIENT_SECRET']);
$vendor->setHostURL($_ENV['AYOCONNECT_HOST_URL']);



/*--------------------------------------  // Get token  -------------------------------------------------------*/
try {
	$result = $vendor->AuthGetToken();
	extract($result);
	// print_r($response);
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
	echo ErrorToString($e);
}



/*--------------------------------------  // Check balance  -------------------------------------------------------*/
// try {
// 	$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
// 	$rand = substr(str_shuffle($permitted_chars), 0, 22);
// 	$balance = new Transaction;
// 	$balance->setTransactionID(time() . $rand);
// 	$result = $vendor->CheckAccountBalance($balance);
// 	extract($result);
// 	print_r($response);
// 	/* Success
// 		{
// 			"status": "000",
// 			"data": {
// 				"code": 200,
// 				"message": "ok",
// 				"responseTime": "20231025171237",
// 				"transactionId": "1698228755TFMV5YCR9EI3OUWASL876Z",
// 				"referenceNumber": "ec6a5f4f374a473382cccfe066dd2333",
// 				"merchantCode": "NSIPAY",
// 				"accountInfo": [
// 						{
// 							"availableBalance": {
// 								"value": "0.00",
// 								"currency": "IDR"
// 							}
// 						}
// 				]
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorToString($e);
// }



/*--------------------------------------  // Beneficiary inquiry  -------------------------------------------------------*/
// try {
// 	$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
// 	$rand = substr(str_shuffle($permitted_chars), 0, 22);
// 	$inquiry = new Transaction;
// 	$inquiry->setTransactionID(time() . $rand);
// 	$inquiry->setCustomerPhone('6281212121314');
// 	$inquiry->setCustomerBankCode('CENAIDJA');
// 	$inquiry->setCustomerBankAccountNumber('543210330');
// 	$inquiry->setIPAddress('180.200.100.10');
// 	$result = $vendor->BankAccountInquiry($inquiry);
// 	extract($result);
// 	print_r($response);
// 	/* Success
// 		{
// 			"status": "000",
// 			"data": {
// 				"code": 202,
// 				"message": "ok",
// 				"responseTime": "20231025101426",
// 				"transactionId": "1698228863BRW7KXCNTOF9G4U5JL3IHP",
// 				"referenceNumber": "c056ac9c257d4b3394bbfdb5063b9190",
// 				"customerId": "NSIPAY-11B3LLUQ",
// 				"beneficiaryDetails": {
// 						"beneficiaryAccountNumber": "543210330",
// 						"beneficiaryBankCode": "CENAIDJA",
// 						"beneficiaryBankName": "Bank BCA",
// 						"beneficiaryId": "BE_c0a5b9f349",
// 						"beneficiaryName": "N\\/A",
// 						"accountType": "N\\/A"
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorToString($e);
// }



/*--------------------------------------  // Fund transfer  -------------------------------------------------------*/
try {
	$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$rand = substr(str_shuffle($permitted_chars), 0, 22);
	$transaction = new Transaction;
	$transaction->setTransactionID(time() . $rand);
	$transaction->setCustomerID('NSIPAY-11B3LLUQ');
	$transaction->setBeneficiaryID('BE_c0a5b9f349');
	$transaction->setAmount(100000);
	$transaction->setCurrency("IDR");
	$transaction->setDescription("TESTTRANSFER");
	$transaction->setIPAddress('180.200.100.10');
	$result = $vendor->FundTransfer($transaction);
	extract($result);
	print_r($response);
	/* Success
	*/
} catch (\Throwable $e) {
	echo ErrorToString($e);
}



/*--------------------------------------  // Check transfer  -------------------------------------------------------*/
// try {
// 	$check = new Transaction;
// 	$check->setTransactionID('16976132629RIF0WT1X23MKVGPJZQ6CB');
// 	$check->setReferenceNumber('2963c24b207947ac8e4030d94fb89464');
// 	$check->setBeneficiaryID('BE_1c976c0458');
// 	$check->setCustomerID('NSIPAY-11B3LLUQ');
// 	$check->setIPAddress('180.200.100.10');
// 	$result = $vendor->CheckFundTransferStatus($check);
// 	extract($result);
// 	print_r($response);
// 	/* Success
// 	*/
// } catch (\Throwable $e) {
// 	echo ErrorToString($e);
// }



/*--------------------------------------  // Test callback  -------------------------------------------------------*/
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
// 	echo ErrorToString($e);
// }


