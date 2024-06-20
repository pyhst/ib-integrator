<?php

use IbIntegrator\Vendors\PaymentGateway\Duitku;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';



$vendor = new Duitku;
$vendor->setID($_ENV['DUITKU_MERCHANT_ID']);
$vendor->setAPIKey($_ENV['DUITKU_API_KEY']);
$vendor->setHostURL($_ENV['DUITKU_HOST_URL']);
$vendor->setParams([
	'DisbursementMerchantID' => $_ENV['DUITKU_DISBURSEMENT_MERCHANT_ID'],
	'DisbursementAPIKey' => $_ENV['DUITKU_DISBURSEMENT_API_KEY'],
	'DisbursementEmail' => $_ENV['DUITKU_DISBURSEMENT_EMAIL'],
]);



/**
 *
 * Billings
 *
 */
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'CreateBilling') {
	try {
		$transaction = new Transaction;
		$transaction->setURL('/webapi/api/merchant/v2/inquiry');
		$transaction->setOrderID(time());
		$transaction->setAmount(100000);
		$transaction->setCustomerName('TESTER');
		$transaction->setPaymentMethod('BC');
		$result = $vendor->CreateBilling($transaction);
		extract($result);
		print_r($response);
		/* Success VA
			{
				"status": "000",
				"data": {
					"merchantOrderId": "1701926600",
					"merchantCode": "DS15995",
					"reference": "DS1599523JY1NIDU23UORZ8U",
					"paymentUrl": "https:\\/\\/sandbox.duitku.com\\/topup\\/topupdirectv2.aspx?ref=BC23GEE8WLWLXIU6OAI",
					"vaNumber": "7007014002758472",
					"amount": "100000",
					"statusCode": "00",
					"statusMessage": "SUCCESS"
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'InquiryPayment') {
	try {
		$inquiry = new Transaction;
		$inquiry->setURL('/webapi/api/merchant/transactionStatus');
		$inquiry->setOrderID('1701926600');
		$result = $vendor->InquiryPayment($inquiry);
		extract($result);
		print_r($response);
		/* Success
			{
				"status": "000",
				"data": {
					"merchantOrderId": "1701926600",
					"reference": "DS1599523JY1NIDU23UORZ8U",
					"amount": "100000",
					"fee": "5000.00",
					"statusCode": "01",
					"statusMessage": "PROCESS"
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'PaymentCallback') {
	try {
		$callback = '
			{
				"merchantCode": "D6677",
				"amount": "100000",
				"merchantOrderId": "0001285662",
				"productDetail": "Payment for order 0001285662",
				"additionalParam": null,
				"resultCode": "00",
				"signature": "439030a6da086ee13558137f07d4a27d",
				"paymentCode": "VC",
				"merchantUserId": null,
				"reference": "D6677JXVYL752HMAV0AD"
			}';
		$callback = json_decode($callback);
		$result = $vendor->PaymentCallback($callback);
		extract($result);
		print_r($response);
		/* Success
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}



/**
 *
 * Disbursement
 *
 */
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'GetBankList') {
	try {
		$transaction = new Transaction;
		$transaction->setURL('/webapi/api/disbursement/listBank');
		$transaction = $vendor->GetBankList($transaction);
		extract($transaction);
		print_r($response);
		/* Success
			{
				"status": "000",
				"data": [
					{
							"name": "BANK CENTRAL ASIA",
							"code": "014",
							"limit": 100000000
					},
					{
							"name": "BANK BRI",
							"code": "002",
							"limit": 100000000
					},
					{
							"name": "BANK  BTN",
							"code": "200",
							"limit": 50000000
					}
				]
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'CheckAccountBalance') {
	try {
		$transaction = new Transaction;
		$transaction->setURL('/webapi/api/disbursement/checkbalance');
		$transaction = $vendor->CheckAccountBalance($transaction);
		extract($transaction);
		print_r($response);
		/* Success
			{
				"status": "000",
				"data": {
					"userId": 32165,
					"email": "dev@np.co.id",
					"balance": 9952000,
					"effectiveBalance": 9952000,
					"responseCode": "00",
					"responseDesc": "Success"
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'BankAccountInquiry') {
	try {
		$transaction = new Transaction;
		//
		// ONLINE TRANSFER
		//$transaction->setURL('/webapi/api/disbursement/inquiry');
		// LLG, RTGS, H2H or BIFAST
		$transaction->setTransferMethod('BIFAST');
		$transaction->setURL('/webapi/api/disbursement/inquiryclearing');
		//
		$transaction->setCustomerBankCode('014');
		$transaction->setCustomerBankAccountNumber('8760673561');
		$transaction->setAmount(10000);
		$transaction->setPurposeOfTransaction('TEST TRANSFER');
		$transaction->setSenderID('001');
		$transaction->setSenderName('SENDER');
		$transaction->setOrderID(time());
		$transaction = $vendor->BankAccountInquiry($transaction);
		extract($transaction);
		print_r($response);
		/* Success
			{
				"status": "000",
				"data": {
					"email": "dev@np.co.id",
					"bankCode": "014",
					"bankAccount": "8760673561",
					"amountTransfer": 10000,
					"accountName": "CATUR NUGROHO                 ",
					"custRefNumber": "1701928724",
					"disburseId": 354687,
					"responseCode": "00",
					"responseDesc": "SUCCESS"
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'FundTransfer') {
	try {
		$transaction = new Transaction;
		//
		// ONLINE TRANSFER
		//$transaction->setURL('/webapi/api/disbursement/transfer');
		// LLG, RTGS, H2H or BIFAST
		$transaction->setTransferMethod('BIFAST');
		$transaction->setURL('/webapi/api/disbursement/transferclearing');
		//
		$transaction->setDisbursementID('354687'); // Single use only, next hit will error with 'Transaction Already Finished'
		$transaction->setCustomerBankCode('014');
		$transaction->setCustomerBankAccountNumber('8760673561');
		$transaction->setAmount(10000); // Must be same as inquiry, or else error with 'Amount transfer is different from inquiry'
		$transaction->setPurposeOfTransaction('TEST TRANSFER');
		$transaction->setSenderID('001');
		$transaction->setSenderName('SENDER');
		$transaction->setOrderID('1701928724');
		$transaction = $vendor->FundTransfer($transaction);
		extract($transaction);
		print_r($response);
		/* Success
			{
				"status": "000",
				"data": {
					"email": "dev@np.co.id",
					"bankCode": "014",
					"bankAccount": "8760673561",
					"amountTransfer": 10000,
					"accountName": "CATUR NUGROHO                 ",
					"custRefNumber": "1701928724",
					"responseCode": "00",
					"responseDesc": "SUCCESS"
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'CheckFundTransferStatus') {
	try {
		$transaction = new Transaction;
		$transaction->setURL('/webapi/api/disbursement/inquirystatus');
		$transaction->setDisbursementID('354687');
		$transaction = $vendor->CheckFundTransferStatus($transaction);
		extract($transaction);
		print_r($response);
		/* Success
			{
				"status": "000",
				"data": {
					"bankCode": "014",
					"bankAccount": "8760673561",
					"amountTransfer": 10000,
					"accountName": "CATUR NUGROHO                 ",
					"custRefNumber": "1701928724",
					"responseCode": "00",
					"responseDesc": "Successful"
				}
			}
		*/
	} catch (\Throwable $e) {
		echo ErrorToString($e);
	}
}
