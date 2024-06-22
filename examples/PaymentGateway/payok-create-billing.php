<?php

use IbIntegrator\Vendors\PaymentGateway\PayOk;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';

$vendor = new PayOk();
$vendor->setID($_ENV['PAYOK_MERCHANT_ID']);
$vendor->setPrivateKey($_ENV['PAYOK_PRIVATE_KEY']);
$vendor->setPublicKey($_ENV['PAYOK_PUBLIC_KEY']);
$vendor->setHostURL($_ENV['PAYOK_HOST_URL']);
$vendor->setPaymentURL($_ENV['PAYOK_HOST_URL']);
$vendor->setRequestURL($_ENV['PAYOK_HOST_URL']);
$vendor->setCallbackURL('http://tester.com/secure/callback/demo');



// /**
//  *
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new Transaction();
// 	$transaction->setAmount(15000);
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setDescription('BILLING1');
// 	$transaction->setCustomerName('TESTER');
// 	$transaction->setCustomerID('1234567890');
// 	$transaction->setCustomerEmail('tester@gmail.com');
// 	$transaction->setCustomerCity('X');
// 	$transaction->setCustomerAddress('X');
// 	$transaction->setCustomerPhone('0811111111111');
// 	//
// 	/* // Channel list:
// 		QRIS:
// 			QRIS
// 		EWALLET:
// 			DANABALANCE
// 			SHOPEEBALANCE
// 			LINKAJABALANCE
// 			OVOBALANCE
// 			GOPAYBALANCE
// 		VA:
// 			SinarmasVA,
// 			MaybankVA,
// 			DanamonVA,
// 			BNCVA,
// 			BCAVA,
// 			INAVA,
// 			BNIVA,
// 			PermataVA,
// 			MuamalatVA,
// 			BSIVA,
// 			BRIVA,
// 			MandiriVA,
// 			CIMBVA
// 	*/
// 	$transaction->setPaymentMethod('QRIS');
// 	$transaction->setPaymentChannel('');
// 	//
// 	// $transaction->setPaymentMethod('EWALLET');
// 	// $transaction->setPaymentChannel('DANABALANCE');
// 	// //
// 	// $transaction->setPaymentMethod('VA');
// 	// $transaction->setPaymentChannel('MuamalatVA');
// 	//
// 	$result = $vendor->CreateBilling($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success VA
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/va\/create",
// 				"data": {
// 						"requestId": "20240622215017",
// 						"merchantId": "010430",
// 						"paymentType": "MuamalatVA",
// 						"amount": "15000.00",
// 						"merchantTradeNo": "1719067817",
// 						"notifyUrl": "http:\/\/tester.com\/secure\/callback\/demo",
// 						"payer": "TESTER",
// 						"productName": "BILLING1",
// 						"productInfo": [
// 							{
// 								"id": "1719067817",
// 								"name": "BILLING1",
// 								"price": "15000.00",
// 								"type": "VA",
// 								"quantity": 1
// 							}
// 						]
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json;charset=utf-8",
// 						"X-TIMESTAMP": "2024-06-22T21:50:17.659+07:00",
// 						"X-SIGNATURE": "ZTD7bCW09iVp13AqLNpsRFsQIINh7pamCjrqm4Wm2sT3r2+ugnC\/hTyFMsWozhTxpQ1vLL\/oNulydaqxRnEDtkw0CH5YT89U48+G\/Ysh\/04+3Yja6vpHMOIFCGbip\/mo6VphOT4fiAEOUgFlq4gDJ\/VaZSk\/yGbCExlFz\/ZSo24YiiNz93C5RdhBXWJPbmMkgbZK2TV1FU9drs1j4uOsD4Qo8\/TXLTHtqgGI2j\/t7vvZPhm+Vby0pnLBvrJLGbHq4g0eh9AN7wwqqlh3tEXZ9OnjE\/HfLhUxZMUKy3wGvdRVPI1GKphTxUXtIkb99mhTbUe0gpLNT0dilro+it4a8Q==",
// 						"X-PARTNER-ID": "010430",
// 						"X-REQUEST-ID": "20240622215017"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"merchantId": "010430",
// 				"requestId": "20240622215017",
// 				"errCode": "0",
// 				"paymentType": "MuamalatVA",
// 				"amount": "15000.00",
// 				"merchantTradeNo": "1719067817",
// 				"createTime": "20240622215018",
// 				"platformTradeNo": "2024062243000000012",
// 				"expiredTime": "20240623215018",
// 				"status": "01",
// 				"productName": "BILLING1",
// 				"productInfo": [
// 						{
// 							"id": "1719067817",
// 							"name": "BILLING1",
// 							"price": 15000,
// 							"type": "VA",
// 							"quantity": 1
// 						}
// 				],
// 				"transFeeRate": "0.000000",
// 				"transFeeAmount": "10000.00",
// 				"totalTransFee": "10000.00",
// 				"vaCode": "9999900000000001"
// 			}
// 		}
// 	*/
// 	/* // Success QRIS
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/qris\/create",
// 				"data": {
// 						"requestId": "20240622215106",
// 						"merchantId": "010430",
// 						"paymentType": "QRIS",
// 						"amount": "15000.00",
// 						"merchantTradeNo": "1719067866",
// 						"notifyUrl": "http:\/\/tester.com\/secure\/callback\/demo",
// 						"productName": "BILLING1",
// 						"expire": 120,
// 						"productInfo": [
// 							{
// 								"id": "1719067866",
// 								"name": "BILLING1",
// 								"price": "15000.00",
// 								"type": "QRIS",
// 								"quantity": 1
// 							}
// 						]
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json;charset=utf-8",
// 						"X-TIMESTAMP": "2024-06-22T21:51:06.660+07:00",
// 						"X-SIGNATURE": "XjM1MEO1I600lLOmhrc5CGiWUg15eJzjBtEp7D\/AfojPAs80S+TDajZA6IDmpTUXjv\/mqbJewVU+JdHGeyoJogdknLwWXlL8oY9P7LPqUKyH\/28d9oFpjP83nqbJwiwo7LNAvPQ0fVIumdK3u\/RxZ45a58yxtSqRaaTYYl7uXgY\/zRL2QCxLS\/M1x9gE9h1s3ycM7UdWzYRnJ9VwK9ryVK0EQYVS1drqxM+C8YuLJtZxvUlIvXuhHu0M2EdIWEzbyGfK8XBRbY5UmDqr3cnlCK1OiU839zfvP\/vNTjm4bef9fAZHJtDQhDNoSXKRBTOCM1+AYYR5JbHqwD3ASmTtKA==",
// 						"X-PARTNER-ID": "010430",
// 						"X-REQUEST-ID": "20240622215106"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"merchantId": "010430",
// 				"requestId": "20240622215106",
// 				"errCode": "0",
// 				"paymentType": "QRIS",
// 				"amount": "15000.00",
// 				"merchantTradeNo": "1719067866",
// 				"createTime": "20240622215106",
// 				"platformTradeNo": "2024062243000000013",
// 				"expiredTime": "20240623215106",
// 				"status": "01",
// 				"productName": "BILLING1",
// 				"productInfo": [
// 						{
// 							"id": "1719067866",
// 							"name": "BILLING1",
// 							"price": 15000,
// 							"type": "QRIS",
// 							"quantity": 1
// 						}
// 				],
// 				"transFeeRate": "0.000000",
// 				"transFeeAmount": "10000.00",
// 				"totalTransFee": "10000.00",
// 				"qrCode": "MOCK-QRIS:2024062243000000013",
// 				"qrisUrl": "https:\/\/sit-payer.paylabs.co.id\/payer-api\/qr?ade21096e817f7c7ed325d9235e96f71MOCK-QRIS%3A2024062243000000013"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }



// /**
//  *
//  * Inquiry trx
//  *
//  */
// try {
// 	$transaction = new Transaction();
// 	// $transaction->setPaymentMethod('QRIS');
// 	// $transaction->setReferenceNumber('1719067866');
// 		$transaction->setPaymentMethod('VA');
// 		$transaction->setPaymentChannel('MuamalatVA');
// 		$transaction->setReferenceNumber('1719067817');
// 	$result = $vendor->InquiryPayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/qris\/query",
// 				"data": {
// 						"requestId": "20240622215258",
// 						"merchantId": "010430",
// 						"merchantTradeNo": "1719067866",
// 						"paymentType": "QRIS"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json;charset=utf-8",
// 						"X-TIMESTAMP": "2024-06-22T21:52:58.661+07:00",
// 						"X-SIGNATURE": "Y9BYd+6Le0GkrJBuM\/oxMK3emEsxnpPcCzzn6AvyTt49+4XRc0kPd8VwnNrBWbcM3Tcw5yC\/qzddlFiYSF67V7YA15mSlCKJdsBuIjbfen1m2VLfd4yuS06jdbza80CtT7OPp26vit6g5j0GjYQKXFI6K+\/STcHvEcqx8GsoMfwUlCmGHufYfq4zrOeq0YKHemg4HHCsFKwgM22AfHkC03+w2m4UJsWwLbdDeR1DZw+uokHqifPXYyEAXvYzpxU1Ni2PGIpxeEwafrVlUbxA6Y1UJNFLrFXyOro9c1JjsTaxmbmdBMALQpEBUXoB0fneXXaDGTJMaf8moMmCDPEKIg==",
// 						"X-PARTNER-ID": "010430",
// 						"X-REQUEST-ID": "20240622215258"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"merchantId": "010430",
// 				"requestId": "20240622215258",
// 				"errCode": "0",
// 				"paymentType": "QRIS",
// 				"amount": "15000.00",
// 				"merchantTradeNo": "1719067866",
// 				"createTime": "20240622215106",
// 				"platformTradeNo": "2024062243000000013",
// 				"successTime": "20240622215208",
// 				"expiredTime": "20240623215106",
// 				"status": "02",
// 				"productName": "BILLING1",
// 				"productInfo": [
// 						{
// 							"id": "1719067866",
// 							"name": "BILLING1",
// 							"price": 15000,
// 							"type": "QRIS",
// 							"quantity": 1
// 						}
// 				],
// 				"transFeeRate": "0.000000",
// 				"transFeeAmount": "10000.00",
// 				"totalTransFee": "10000.00",
// 				"qrCode": "MOCK-QRIS:2024062243000000013",
// 				"qrisUrl": "https:\/\/sit-payer.paylabs.co.id\/payer-api\/qr?ade21096e817f7c7ed325d9235e96f71MOCK-QRIS%3A2024062243000000013"
// 			}
// 		}
// 	*/
// 	/* // Success inquiry VA
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/sit-pay.paylabs.co.id\/payment\/v2.1\/va\/query",
// 				"data": {
// 						"requestId": "20240622215409",
// 						"merchantId": "010430",
// 						"merchantTradeNo": "1719067817",
// 						"paymentType": "MuamalatVA"
// 				},
// 				"headers": {
// 						"Content-Type": "application\/json;charset=utf-8",
// 						"X-TIMESTAMP": "2024-06-22T21:54:09.662+07:00",
// 						"X-SIGNATURE": "MEmeZgzf\/r5ZMcXr3AkT01BybT182Eeb77AQi79m4cPFNW\/CfCTlFh5S2wDdgWFc+yJ+LCOO99QyOHLIq3bZwEW7fT995Ih0DaZ7+JDt0vElAoLTYpxUZSBxauRv0z3s0Sf+VXeTusbvEM+9jXrGvlElhLCCv6T7hjk+pgXpgaKZAb8vol2M1cAg7BbBtiunimiYAIMasx\/4NZkwJNbxzp42T+4z49O6csoVYtJIW8fLPri73W7bStFaRVD1jujWuWcO00hrVv+OK+rvCCEkelMsti+h7M24iTVkreSYVLGWYTFGYb+RbrCPJhJ0Rs0ZQpC7o8NknN6UOc+uQSQrWA==",
// 						"X-PARTNER-ID": "010430",
// 						"X-REQUEST-ID": "20240622215409"
// 				},
// 				"options": {
// 						"as_json": true
// 				}
// 			},
// 			"data": {
// 				"merchantId": "010430",
// 				"requestId": "20240622215409",
// 				"errCode": "0",
// 				"paymentType": "MuamalatVA",
// 				"amount": "15000.00",
// 				"merchantTradeNo": "1719067817",
// 				"createTime": "20240622215018",
// 				"platformTradeNo": "2024062243000000012",
// 				"successTime": "20240622215119",
// 				"expiredTime": "20240623215018",
// 				"status": "02",
// 				"productName": "BILLING1",
// 				"productInfo": [
// 						{
// 							"id": "1719067817",
// 							"name": "BILLING1",
// 							"price": 15000,
// 							"type": "VA",
// 							"quantity": 1
// 						}
// 				],
// 				"transFeeRate": "0.000000",
// 				"transFeeAmount": "10000.00",
// 				"totalTransFee": "10000.00",
// 				"vaCode": "9999900000000001"
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // $vendor->ThrowErrorException($e);
// }
