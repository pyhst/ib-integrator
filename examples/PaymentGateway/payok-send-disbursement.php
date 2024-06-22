<?php

use IbIntegrator\Vendors\PaymentGateway\PayOk;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';

$vendor = new PayOk();
$vendor->setID($_ENV['PAYOK_MERCHANT_ID']);
$vendor->setPrivateKey($_ENV['PAYOK_PRIVATE_KEY']);
$vendor->setPublicKey($_ENV['PAYOK_PUBLIC_KEY']);
$vendor->setHostURL($_ENV['PAYOK_HOST_URL_DISBURSEMENT']);
$vendor->setRequestURL($_ENV['PAYOK_HOST_URL_DISBURSEMENT']);
$vendor->setCallbackURL('http://tester.com/secure/callback/demo');



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
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/balanceQuery",
// 				"data": {
// 						"partnerReferenceNo": "1719068395",
// 						"accountNo": "010430",
// 						"balanceTypes": "CASH"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-22T14:59:55.534Z",
// 						"X-SIGNATURE": "QuHrhkIlOw\/NN98qAK9hrHWswqzGDQMqKkH8T2ljkmnwFNPltl3C+tHOt5vuyFH0NVBcWjF5VRBR5l8cnA\/v\/Y9U0VbqNV3JTCJRFU6\/5DzFyZ8WYCq+YDg5n7z5T00bOl1CM3VHl24hBkDRCQsurKovLFElGylDu3zmTdR\/TQS6iQNdwPiGBvfs826ibRE5R+jtP5H0kKLRjqM9wtFSNTBifSmi8I2iiO5grotnSoPvUx3n9NbdO9u4KtlHDlZl55jIukBOtCPJ5CqaB4ACCTf1vTvhpuG\/Zv8qza2vM0IWfMo0yLpEdxodiBWXPcp548c6Q8+aHzjaJXw2fvKD0A==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240622145955",
// 						"ORIGIN": "-",
// 						"CHANNEL-ID": "-"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"referenceNo": "2024062243090000001",
// 				"accountNo": "2406040000010430",
// 				"name": "Test Merchant",
// 				"partnerReferenceNo": "1719068395",
// 				"responseMessage": "Request has been processed successfully",
// 				"accountInfos": {
// 						"amount": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"holdAmount": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"floatAmount": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"ledgerBalance": {
// 							"value": 5022985,
// 							"currency": "Rp"
// 						},
// 						"availableBalance": {
// 							"value": 4938000,
// 							"currency": "Rp"
// 						},
// 						"currentMultilateralLimit": {
// 							"value": 0,
// 							"currency": "Rp"
// 						},
// 						"status": "Available",
// 						"balanceType": "Cash",
// 						"registrationStatusCode": "registered"
// 				},
// 				"responseCode": "SUCCESS"
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
// 	$transaction->setReferenceNumber($time);
// 	$transaction->setAmount(10000);
// 	$transaction->setCustomerBankCode('011');
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$result = $vendor->BankAccountInquiry($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success bank account inquiry
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/remitInquiry",
// 				"data": {
// 						"partnerReferenceNo": "1719068496",
// 						"beneficiaryAccountNo": "7700173383",
// 						"beneficiaryBankCode": "011",
// 						"additionalInfo": {
// 							"remitType": "BANK",
// 							"amount": 10000
// 						}
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-22T15:01:36.926Z",
// 						"X-SIGNATURE": "NigrFDOh2Qlnay2umsUGsDr2yvobkyS7HI5UwXT\/lHfuatck\/DcjcmvEz5xiyf+pgChG+QfwX2vsZYdIsd\/Vo0K6M3doVCckU6uw2JoeVYCgmz\/ShhMc3Fbu6uvh2rVZEdTXvoK71OW7ZmVw6tktSWO\/e+wVJoxxiWvdhOaDyGFdGfwld\/Eay8Vz9+Xu9IOLDh6B3VxDKCFrzr6qQcL9H4pzEEFiofkeFRS0a9IK0oWQnJS32e63\/KATPQ+BobkBk9xIeJK3n55rDJfCqRqM6GUmn255waJNDxmINsRW7G9Gp0tvc7rE7rSYxDrgbtr66yqCVGPojb31oezbN+SCJQ==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240622150136",
// 						"ORIGIN": "-",
// 						"CHANNEL-ID": "-"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"beneficiaryAccountNo": "7700173383",
// 				"beneficiaryAccountName": "7700173383",
// 				"referenceNo": "2024062243090000003",
// 				"beneficiaryBankName": "BANK DANAMON",
// 				"partnerReferenceNo": "1719068496",
// 				"currency": "Rp",
// 				"beneficiaryBankCode": "011",
// 				"responseMessage": "Request has been processed successfully",
// 				"responseCode": "SUCCESS"
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
// 	$transaction->setReferenceNumber(date('YmdHis', $time));
// 	$transaction->setAmount(10000);
// 	$transaction->setTransactionID('2024062243090000003');
// 	$transaction->setCustomerBankAccountNumber('7700173383');
// 	$transaction->setCustomerEmail('tester@gmail.com');
// 	$transaction->setTransactionDate(date('YmdHis', $time));
// 	$transaction->setRemark('TEST-TRANSFER');
// 	$transaction->setParams([
// 		'SOURCE_ACCOUNT_NUMBER' => '2406040000010430',
// 	]);
// 	$result = $vendor->FundTransfer($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success disb
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/remitCreate",
// 				"data": {
// 						"partnerReferenceNo": "20240622220456",
// 						"feeType": "OUR",
// 						"currency": "Rp",
// 						"amount": {
// 							"value": 10000,
// 							"currency": "Rp"
// 						},
// 						"customerReference": "2024062243090000003",
// 						"beneficiaryAccountNo": "7700173383",
// 						"beneficiaryEmail": "tester@gmail.com",
// 						"transactionDate": "20240622220456",
// 						"sourceAccountNo": "2406040000010430",
// 						"remark": "TEST-TRANSFER"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-22T15:04:56.047Z",
// 						"X-SIGNATURE": "K+utRdjsG93lZ78Ak37SAZUz4P3Wd4ucbE9awb4S\/EXDHaWvGSEqwbfIrD2yMY0ukvUZmwoOWQwvIE4WPqZXxgL+bOj2Ana0E075RIO\/X2Vo\/GuIm6y2yzMlu9cTCtLPcug5KO20X\/K6zR7FO2xhLOlz36fdDo36gVv0pSkng+AXRfTi9tiTLcs6ydQqOdcNPNe3ZCg7gEgOpqogeE09LD4JMcsLbIZMwngN\/mHcn\/GDIK7\/a4X79cN6tpt6CPBFPuCUNPmmL0bdm3b8vXhDolsxSM+dhxd2otOaQG2ZlFOiV316RenLjF0+60ZnNPL1WALa4SAs5HR7MfGKPozMsw==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240622150456",
// 						"ORIGIN": "-",
// 						"CHANNEL-ID": "-"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"beneficiaryAccountNo": "7700173383",
// 				"amount": {
// 						"currency": "Rp",
// 						"value": 10000
// 				},
// 				"referenceNo": "2024062243090000003",
// 				"partnerReferenceNo": "20240622220456",
// 				"beneficiaryBankCode": "011",
// 				"responseMessage": "Request has been processed successfully",
// 				"sourceAccountNo": "010430",
// 				"responseCode": "SUCCESS"
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
// 	$transaction->setReferenceNumber('20240622220456');
// 	$transaction->setOriginalReferenceNumber('2024062243090000003');
// 	$transaction->setExternalID('20240622150456');
// 	$transaction->setTransactionDate('20240622220456');
// 	$transaction->setAmount(10000);
// 	$result = $vendor->CheckFundTransferStatus($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success check status
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-remit-api.paylabs.co.id\/api-pay\/remit\/v1.1\/remitQuery",
// 				"data": {
// 						"originalPartnerReferenceNo": "20240622220456",
// 						"originalReferenceNo": "2024062243090000003",
// 						"originalExternalId": "20240622150456",
// 						"serviceCode": "remit.query",
// 						"transactionDate": "20240622220456",
// 						"amount": {
// 							"value": 10000,
// 							"currency": "Rp"
// 						}
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json",
// 						"X-TIMESTAMP": "2024-06-22T15:08:19.975Z",
// 						"X-SIGNATURE": "dqYFJArYLqnAXG41nuMPVTzR\/+a8TfB4xI3TGEDuG2X9\/Xh5s5UBMHz0aosW5uLf+xN2dBCyq6OHnMoW3ZbK+zmz5pQK205Uva+55F1xcTUt9bLbPdNB0GRhAANo\/5uTtrs7IPZXvq7+5DvA4ANiAj7\/vQcommAwySJILovPkcSBN4OZRHlQhpkXuIKEpW8qN1Apzf0h8rlWVyoLyjav34hdpHYcKxkCY5ks1fKahV6\/BCOn5yK1\/+5vvtFz9dsOErzFyo+N57oWgSHh5BgcKTbpSkg0QAf\/\/z+UP+wuzT2hv2Svh\/pqWYf8msrX0Jj7eFY2lYtNc6nh\/CIqbkob9A==",
// 						"X-PARTNER-ID": "010430",
// 						"X-EXTERNAL-ID": "20240622150819",
// 						"ORIGIN": "-",
// 						"CHANNEL-ID": "-"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"beneficiaryAccountNo": "7700173383",
// 				"amount": {
// 						"currency": "Rp",
// 						"value": 10000
// 				},
// 				"originalReferenceNo": "2024062243090000003",
// 				"referenceNumber": "20240622150456",
// 				"originalExternalId": "20240622150456",
// 				"serviceCode": "18",
// 				"latestTransactionStatus": "SUCCESS",
// 				"transactionStatusDesc": "Successful",
// 				"originalPartnerReferenceNo": "20240622220456",
// 				"responseMessage": "Request has been processed successfully",
// 				"sourceAccountNo": "010430",
// 				"responseCode": "SUCCESS"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }