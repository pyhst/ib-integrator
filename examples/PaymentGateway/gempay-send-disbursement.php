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
$vendor->setCallbackURL('http://tester.com/secure/callback/demo');
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
// 			"request": {
// 				"url": "https:\/\/sandbox.gempay.online\/api\/balance_query",
// 				"data": {
// 						"merchant_id": "KMB0001",
// 						"request_id": "CB1719205793",
// 						"signature": "934b7003eda1f40c40313a89292f7dc2"
// 				},
// 				"headers": {
// 						"Accept": "application\/json"
// 				},
// 				"options": []
// 			},
// 			"data": {
// 				"result": true,
// 				"error_code": "000",
// 				"error_desc": "Data found",
// 				"data": {
// 						"merchant_id": "KMB0001",
// 						"active_balance": "969400",
// 						"pending_balance": "800000"
// 				}
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
// 			"request": {
// 				"url": "https:\/\/sandbox.gempay.online\/api\/inquiry",
// 				"data": {
// 						"merchant_id": "KMB0001",
// 						"project_no": "UJK87AA",
// 						"request_id": "BAI1719205819",
// 						"amount": 10000,
// 						"remit_type": "bank",
// 						"partner_ref_id": "1719205819",
// 						"account_no": "7700173383",
// 						"bank_code": "011",
// 						"transaction_datetime": "2024-06-24 12:10:19",
// 						"signature": "5a05f2962d15251b6ac42d3c82328e60"
// 				},
// 				"headers": {
// 						"Accept": "application\/json"
// 				},
// 				"options": []
// 			},
// 			"data": {
// 				"result": true,
// 				"error_code": "000",
// 				"error_desc": "Data Found",
// 				"data": {
// 						"partner_ref_id": "1719205819",
// 						"inquiry_id": "240624121020J999",
// 						"bank_code": "011",
// 						"account_name": "7700173383",
// 						"account_no": "7700173383",
// 						"account_bank": "Bank Danamon & Danamon Syariah",
// 						"amount": 10000,
// 						"admin_fee": 2000,
// 						"total_amount": 12000,
// 						"transaction_datetime": "2024-06-24 12:10:19",
// 						"merchant_id": "KMB0001",
// 						"project_no": "UJK87AA"
// 				}
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
// 	$transaction->setOrderID('240624121020J999');
// 	$transaction->setDescription('TEST-TRANSFER');
// 	$transaction->setTransactionDate(date('Y-m-d H:i:s', $time));
// 	$result = $vendor->FundTransfer($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sandbox.gempay.online\/api\/transfer",
// 				"data": {
// 						"merchant_id": "KMB0001",
// 						"project_no": "UJK87AA",
// 						"request_id": "TF1719205850",
// 						"inquiry_id": "240624121020J999",
// 						"description": "TEST-TRANSFER",
// 						"transaction_datetime": "2024-06-24 12:10:50",
// 						"signature": "32fadefd8e71cb6cfe4a0d0b82d6721c"
// 				},
// 				"headers": {
// 						"Accept": "application\/json"
// 				},
// 				"options": []
// 			},
// 			"data": {
// 				"result": true,
// 				"error_code": "000",
// 				"error_desc": "Disburse Successful",
// 				"data": {
// 						"partner_ref_id": "1719205819",
// 						"bank_code": "011",
// 						"account_no": "7700173383",
// 						"account_bank": "Bank Danamon & Danamon Syariah",
// 						"amount": 10000,
// 						"admin_fee": 2000,
// 						"total_amount": 12000,
// 						"transaction_datetime": "2024-06-24 12:10:50",
// 						"merchant_id": "KMB0001",
// 						"project_no": "UJK87AA",
// 						"ref_id": "2406241210515DTW",
// 						"status": "success"
// 				}
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
// 		'partner_ref_id' => '1719205819',
// 	]);
// 	$result = $vendor->CheckFundTransferStatus($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check status
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sandbox.gempay.online\/api\/status_query",
// 				"data": {
// 						"merchant_id": "KMB0001",
// 						"request_id": "CTF1719205881",
// 						"partner_ref_id": "1719205819",
// 						"signature": "5e00a33e8595120cfa09f4c396ca5905"
// 				},
// 				"headers": {
// 						"Accept": "application\/json"
// 				},
// 				"options": []
// 			},
// 			"data": {
// 				"result": true,
// 				"error_code": "000",
// 				"error_desc": "Data found",
// 				"data": {
// 						"transaction_datetime": "2024-06-24 12:10:50",
// 						"account_no": "7700173383",
// 						"account_bank": "Bank Danamon & Danamon Syariah",
// 						"amount": "10000",
// 						"description": "TEST-TRANSFER",
// 						"merchant_id": "KMB0001",
// 						"status": "Success",
// 						"admin_fee": "2000",
// 						"partner_ref_id": "1719205819",
// 						"ref_id": "2406241210515DTW"
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }