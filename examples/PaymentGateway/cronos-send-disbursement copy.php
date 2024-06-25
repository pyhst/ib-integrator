<?php

use IbIntegrator\Vendors\PaymentGateway\Cronos;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';

$vendor = new Cronos();
$vendor->setID($_ENV['CHRONOS_KEY']);
$vendor->setMerchantToken($_ENV['CHRONOS_TOKEN']);
$vendor->setHostURL($_ENV['CHRONOS_HOST_URL']);
$vendor->setPaymentURL($_ENV['CHRONOS_HOST_URL']);
$vendor->setRequestURL($_ENV['CHRONOS_HOST_URL']);
$vendor->setCallbackURL('http://tester.com/secure/callback/demo');
$vendor->setReturnURL('http://tester.com/secure/callback/demo');
$vendor->setParams([
	//
]);



// /**
//  *
//  * Check Balance
//  *
//  */
// try {
// 	$transaction = new Transaction();
// 	$transaction->setReferenceNumber(time());
// 	$result = $vendor->CheckAccountBalance($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check balance
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/balance",
// 				"headers": {
// 						"On-Key": "SC-KRW9ESNZUUKQXOOX",
// 						"On-Token": "jlaVHot4XGnCqYU8FI20GHwkv6RMOT2t",
// 						"On-Signature": "a57c04cf4f2a33021dc0d4e8a74ab8b24d896c96400434c8c9398d145be108a36e389813daff66cfe93532bf08cbead573336330dcafa490d4e03607d0329040",
// 						"Accept": "application\/json"
// 				}
// 			},
// 			"data": {
// 				"responseCode": 200,
// 				"responseMessage": "success",
// 				"responseData": {
// 						"active": 1933280,
// 						"pending": 0,
// 						"total": 1933280
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
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('011');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success bank account inquiry
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/account-inquiry",
// 				"data": {
// 						"bankCode": "011",
// 						"accountNumber": "7700173383",
// 						"reference": "1719308559",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						}
// 				},
// 				"headers": {
// 						"On-Key": "SC-KRW9ESNZUUKQXOOX",
// 						"On-Token": "jlaVHot4XGnCqYU8FI20GHwkv6RMOT2t",
// 						"On-Signature": "b2a4d02d54c9b4c56235edd087a5a1a6a7d83061103a21b0fef583bc4fb5f9b71c9a6e91e1b3a347d33cd7036b1997e3892672aa29b0e6801d7c985634aba72b",
// 						"Accept": "application\/json"
// 				},
// 				"option": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"responseCode": 200,
// 				"responseMessage": "success",
// 				"responseData": {
// 						"id": "9a1ad912-13a0-4a59-8de3-76ec0b278330",
// 						"status": "pending",
// 						"accountNumber": "7700173383",
// 						"accountName": "Ms. Luisa Berge",
// 						"bankCode": "011",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						}
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
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setAmount(10500);
// 	$result = $vendor->FundTransfer($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/disburse",
// 				"data": {
// 						"bankCode": "011",
// 						"recipientAccount": "7700173383",
// 						"reference": "1719308808",
// 						"amount": 10500,
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						}
// 				},
// 				"headers": {
// 						"On-Key": "SC-KRW9ESNZUUKQXOOX",
// 						"On-Token": "jlaVHot4XGnCqYU8FI20GHwkv6RMOT2t",
// 						"On-Signature": "44cc3de284c721700eb64878b434b982088e9ad4c28d2b72d7d879c1bbc75b2f6e9e226f5673175c20050246fe6270ee15333b1b643845e2e42d81104872d0df",
// 						"Accept": "application\/json"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"responseCode": 200,
// 				"responseMessage": "success",
// 				"responseData": {
// 						"id": "ed2e4a12-5997-40a8-8e7e-9591679de41e",
// 						"merchantRef": "1719308808",
// 						"status": "pending",
// 						"feePayer": "customer",
// 						"amount": 10500,
// 						"fee": 3000,
// 						"totalAmount": 7500,
// 						"expiredDate": "2024-06-25T17:46:49+07:00",
// 						"paidDate": null,
// 						"settleDate": "2024-06-25T16:46:49+07:00",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						},
// 						"disbursement": {
// 							"bankCode": "011",
// 							"recipientAccount": "7700173383",
// 							"recipientName": null
// 						}
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
// 	$transaction = new Transaction();
// 	$transaction->setParams([
// 		'id' => 'ed2e4a12-5997-40a8-8e7e-9591679de41e',
// 		'resend_callback' => 0,
// 	]);
// 	$result = $vendor->CheckFundTransferStatus($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check status
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/check\/ed2e4a12-5997-40a8-8e7e-9591679de41e?resendCallback=0",
// 				"headers": {
// 						"On-Key": "SC-KRW9ESNZUUKQXOOX",
// 						"On-Token": "jlaVHot4XGnCqYU8FI20GHwkv6RMOT2t",
// 						"On-Signature": "a57c04cf4f2a33021dc0d4e8a74ab8b24d896c96400434c8c9398d145be108a36e389813daff66cfe93532bf08cbead573336330dcafa490d4e03607d0329040",
// 						"Accept": "application\/json"
// 				}
// 			},
// 			"data": {
// 				"responseCode": 200,
// 				"responseMessage": "success",
// 				"responseData": {
// 						"id": "ed2e4a12-5997-40a8-8e7e-9591679de41e",
// 						"merchantRef": "1719308808",
// 						"status": "pending",
// 						"feePayer": "customer",
// 						"amount": 10500,
// 						"fee": 3000,
// 						"totalAmount": 7500,
// 						"expiredDate": "2024-06-25T17:46:49+07:00",
// 						"paidDate": null,
// 						"settleDate": "2024-06-25T16:46:49+07:00",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						},
// 						"disbursement": {
// 							"bankCode": "011",
// 							"recipientAccount": "7700173383",
// 							"recipientName": null
// 						}
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }