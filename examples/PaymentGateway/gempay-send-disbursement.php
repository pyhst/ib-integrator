<?php

use IbIntegrator\Vendors\PaymentGateway\Gempay;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';

$vendor = new Gempay();
$vendor->setID($_ENV['GEMPAY_MERCHANT_ID']);
$vendor->setSecret($_ENV['GEMPAY_SECRET']);
$vendor->setHostURL($_ENV['GEMPAY_HOST_URL']);
$vendor->setPaymentURL($_ENV['GEMPAY_HOST_URL']);
$vendor->setRequestURL($_ENV['GEMPAY_HOST_URL']);
$vendor->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');
$vendor->setParams([
	'GEMPAY_PROJECT_NO' => $_ENV['GEMPAY_PROJECT_NO'],
]);



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$result = $vendor->CheckAccountBalance(new Transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check balance
// 		{
// 			"status": "000",
// 			"data": {
// 				"merchant_id": "KMB0001",
// 				"active_balance": "1079400",
// 				"pending_balance": "800000"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Bank account inquiry
//  *
//  */
// try {
// 	$time = time();
// 	$transaction = new Transaction();
// 	// $transaction->setRequestID($time);
// 	$transaction->setReferenceNumber($time);
// 	$transaction->setAmount(10000);
// 	$transaction->setTransferMethod('bank'); // bank / wallet
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setTransactionDate(date('Y-m-d H:i:s', $time));
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success bank account inquiry
// 		{
// 			"status": "000",
// 			"data": {
// 				"partner_ref_id": "1716270835",
// 				"inquiry_id": "240521125358QZHN",
// 				"bank_code": "011",
// 				"account_name": "7700173383",
// 				"account_no": "7700173383",
// 				"account_bank": "Bank Danamon & Danamon Syariah",
// 				"amount": 10000,
// 				"admin_fee": 2000,
// 				"total_amount": 12000,
// 				"transaction_datetime": "2024-05-21 12:53:55",
// 				"merchant_id": "KMB0001",
// 				"project_no": "UJK87AA"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Send disbursement
//  *
//  */
// try {
// 	$time = time();
// 	$transaction = new Transaction();
// 	$transaction->setOrderID('240521130455ZGLJ');
// 	$transaction->setDescription('TEST-TRANSFER');
// 	$transaction->setTransactionDate(date('Y-m-d H:i:s', $time));
// 	$result = $vendor->FundTransfer($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"data": {
// 				"partner_ref_id": "1716271492",
// 				"bank_code": "011",
// 				"account_no": "7700173383",
// 				"account_bank": "Bank Danamon & Danamon Syariah",
// 				"amount": 10000,
// 				"admin_fee": 2000,
// 				"total_amount": 12000,
// 				"transaction_datetime": "2024-05-21 13:05:04",
// 				"merchant_id": "KMB0001",
// 				"project_no": "UJK87AA",
// 				"ref_id": "240521130507N64P",
// 				"status": "success"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Check Transfer Status
//  *
//  */
// try {
// 	$transaction = new Transaction;
// 	$transaction->setParams([
// 		'partner_ref_id' => '1716271492',
// 	]);
// 	$result = $vendor->CheckFundTransferStatus($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check status
// 		{
// 			"status": "000",
// 			"data": {
// 				"transaction_datetime": "2024-05-21 13:05:04",
// 				"account_no": "7700173383",
// 				"account_bank": "Bank Danamon & Danamon Syariah",
// 				"amount": "10000",
// 				"description": "TEST-TRANSFER",
// 				"merchant_id": "KMB0001",
// 				"status": "Success",
// 				"admin_fee": "2000",
// 				"partner_ref_id": "1716271492",
// 				"ref_id": "240521130507N64P"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }