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
		echo ErrorToString($e);
	}



/*--------------------------------------  // Check balance  -------------------------------------------------------*/
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'CheckAccountBalance') {
	try {
		$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rand = substr(str_shuffle($permitted_chars), 0, 22);
		$balance = new Transaction;
		$balance->setTransactionID(time() . $rand);
		$balance->setCorrelationID(time() . $rand);
		$result = $vendor->CheckAccountBalance($balance);
		extract($result);
		print_r($response);
		/* Success
			{
				"status": "000",
				"data": {
					"code": 200,
					"message": "ok",
					"responseTime": "20231025171237",
					"transactionId": "1698228755TFMV5YCR9EI3OUWASL876Z",
					"referenceNumber": "ec6a5f4f374a473382cccfe066dd2333",
					"merchantCode": "NSIPAY",
					"accountInfo": [
							{
								"availableBalance": {
									"value": "0.00",
									"currency": "IDR"
								}
							}
					]
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}



/*--------------------------------------  // Beneficiary inquiry  -------------------------------------------------------*/
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'BankAccountInquiry') {
	try {
		$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rand = substr(str_shuffle($permitted_chars), 0, 22);
		$inquiry = new Transaction;
		$inquiry->setTransactionID(time() . $rand);
		$inquiry->setCorrelationID(time() . $rand);
		$inquiry->setCustomerPhone('6281212121314');
		$inquiry->setCustomerBankCode('CENAIDJA');
		$inquiry->setCustomerBankAccountNumber('55772013');
		$inquiry->setIPAddress('180.200.100.10');
		$result = $vendor->BankAccountInquiry($inquiry);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"code": 202,
					"message": "ok",
					"responseTime": "20231101132631",
					"transactionId": "1698845187SCFTZY0XI6D3EO478VUWBL",
					"referenceNumber": "822797b3c253449db78f721210ff1d2b",
					"customerId": "NSIPAY-11B3LLUQ",
					"beneficiaryDetails": {
							"beneficiaryAccountNumber": "55772013",
							"beneficiaryBankCode": "CENAIDJA",
							"beneficiaryBankName": "Bank BCA",
							"beneficiaryId": "BE_54e892d634",
							"beneficiaryName": "N\\/A",
							"accountType": "N\\/A"
					}
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}



/*--------------------------------------  // Fund transfer  -------------------------------------------------------*/
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FundTransfer') {
	try {
		$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rand = substr(str_shuffle($permitted_chars), 0, 22);
		$transaction = new Transaction;
		$transaction->setTransactionID(time() . $rand);
		$transaction->setCorrelationID(time() . $rand);
		$transaction->setCustomerID('NSIPAY-11B3LLUQ');
		$transaction->setBeneficiaryID('BE_54e892d634');
		$transaction->setAmount(100000);
		$transaction->setCurrency("IDR");
		$transaction->setDescription("TESTTRANSFER");
		$transaction->setIPAddress('180.200.100.10');
		$result = $vendor->FundTransfer($transaction);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"code": 202,
					"message": "ok",
					"responseTime": "20231101132704",
					"transactionId": "1698845220M4JA2KEFNVUQ3L76TOP8Y9",
					"referenceNumber": "1d992bccfdb64fce9aa27b8ece8c11ce",
					"customerId": "NSIPAY-11B3LLUQ",
					"transaction": {
							"beneficiaryId": "BE_54e892d634",
							"status": 0,
							"referenceNumber": "1d992bccfdb64fce9aa27b8ece8c11ce",
							"amount": "100000.00",
							"currency": "IDR"
					}
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}



/*--------------------------------------  // Check transfer  -------------------------------------------------------*/
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'CheckFundTransferStatus') {
	try {
		$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rand = substr(str_shuffle($permitted_chars), 0, 22);
		$check = new Transaction;
		$check->setCorrelationID(time() . $rand);
		$check->setTransactionID('1698845220M4JA2KEFNVUQ3L76TOP8Y9');
		$check->setReferenceNumber('1d992bccfdb64fce9aa27b8ece8c11ce');
		$check->setBeneficiaryID('BE_54e892d634');
		$check->setCustomerID('NSIPAY-11B3LLUQ');
		$check->setIPAddress('180.200.100.10');
		$result = $vendor->CheckFundTransferStatus($check);
		extract($result);
		print_r($response);
		/* // Success
			{
				"status": "000",
				"data": {
					"code": 202,
					"message": "ok",
					"responseTime": "20231101133021",
					"transactionId": "1698845220M4JA2KEFNVUQ3L76TOP8Y9",
					"referenceNumber": "a480e7cc727e48dba7d3f64f53e7214f",
					"customerId": "NSIPAY-11B3LLUQ",
					"transaction": {
							"beneficiaryId": "BE_54e892d634",
							"status": 1,
							"referenceNumber": "1d992bccfdb64fce9aa27b8ece8c11ce",
							"amount": "100000.00",
							"currency": "IDR",
							"remark": "TESTTRANSFER"
					}
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}



/*--------------------------------------  // Test callback  -------------------------------------------------------*/
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'DisbursementCallback') {
	try {
		$callback = '
			{
			}';
		$callback = json_decode($callback);
		$result = $vendor->DisbursementCallback($callback);
		extract($result);
		print_r($response);
		/* Success
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
