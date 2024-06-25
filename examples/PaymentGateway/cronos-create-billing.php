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
//  * Create billing
//  *
//  */
// try {
// 	$transaction = new Transaction();
// 	$transaction->setCustomerID('1234567890');
// 	$transaction->setCustomerName('Testing');
// 	$transaction->setCustomerPhone('081212121314');
// 	$transaction->setAmount(15000);
// 	$transaction->setReferenceNumber(time());
// 	$transaction->setDescription('BILLING1');
// 	$transaction->setExpireIn(10);
// 	//
// 	/*
// 		Bank Code	Bank	Capability
// 		008	Mandiri	One-off / Persistent - Open Amount, Closed Amount
// 		014	BCA	One-off / Persistent - Open Amount, Closed Amount
// 		002	BRI	One-off / Persistent - Open Amount, Closed Amount
// 		009	BNI	One-off - Closed Amount
// 		013	Permata	One-off / Persistent - Open Amount, Closed Amount
// 		011	Danamon	Persistent - Open Amount
// 		022	CIMB	One-off - Closed Amount
// 		153	Sahabat Sampoerna	One-off / Persistent - Open Amount, Closed Amount
// 	*/
// 	// $transaction->setPaymentMethod('va');
// 	// $transaction->setPaymentChannel('014');
// 	//
// 	// $transaction->setPaymentMethod('qris');
// 	// $transaction->setPaymentChannel('qris');
// 	//
// 	$transaction->setPaymentMethod('ewallet');
// 	$transaction->setPaymentChannel('ovo'); // ovo, dana, shopeepay, linkaja
// 	// $transaction->setPaymentChannel('shopeepay');
// 	//
// 	// $transaction->setPaymentMethod('cstore'); // alfamart, indomaret
// 	// $transaction->setPaymentChannel('alfamart');
// 	// $transaction->setPaymentMethod('cc');
// 	//
// 	$result = $vendor->CreateBilling($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success QRIS
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/qris",
// 				"data": {
// 						"reference": "1719307879",
// 						"amount": 15000,
// 						"expiryMinutes": 10,
// 						"viewName": "Testing",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						}
// 				},
// 				"headers": {
// 						"On-Key": "SC-KRW9ESNZUUKQXOOX",
// 						"On-Token": "jlaVHot4XGnCqYU8FI20GHwkv6RMOT2t",
// 						"On-Signature": "6f44e640a571a97e8965d54c7e14b85f257ca50eac44efd18295191cad9bd5e5936b9db8628866ff06d93debe89416ee262cc7ffa26d9d239ddeec4292242764",
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
// 						"id": "aeac88d3-2cbb-4fdd-ade7-d6692b169b43",
// 						"merchantRef": "1719307879",
// 						"status": "pending",
// 						"feePayer": "customer",
// 						"amount": 15000,
// 						"fee": 150,
// 						"totalAmount": 15150,
// 						"expiredDate": "2024-06-25T16:41:20+07:00",
// 						"paidDate": null,
// 						"settleDate": "2024-06-25T16:31:20+07:00",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						},
// 						"qris": {
// 							"content": "Omnis exercitationem velit odit suscipit. Soluta eligendi dolor non sed facilis tempore. Excepturi et ad sit incidunt. Nobis cum voluptates ipsam non.",
// 							"image": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAe8AAAHvCAYAAAB9iVfNAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAN0ElEQVR4nO3cwXLbMLZF0edX+f9fVk8zcKXRMRjcDa41dlmURHEXJufr8\/n8HwDQ8f+nLwAA+N+INwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxPx66h9\/fX19nvrfZZ\/P52vl7059fqeub\/V1V91yfdO\/j+m\/87e9j1XT779b7P7efufkDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADGPLaytenKB5l+avjB06nN+2+uu2r1c9bYltrd9v6t23y+7Tf\/eVk143jt5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMzxhbVVpxZt3rZENH2hacKy0Z\/cspy2+\/pueR\/T779bvO15\/zecvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASAms7DG904tEe1+3enLRtOvb9X0ZbxbPufpfB99Tt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQY2FtqN0LSKf+327Tr2\/3607\/f7udWu47tTg2\/X5mLidvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiMksrO1eNrrFLQtcuxeuTt0v0xe9Vk1f5LtlmWz6fXDK297v33DyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjjC2vTF5CmO7XoNX2Ba\/r73W369a1y\/zX\/3yrP+32cvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW16UtO\/Mz073f6ctVu05fdTv2\/6W5ZTpv+PLiRkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEPLawdsvS1O73cer6vO7P\/u7U\/XzL4tj03+Up7r+fmX5fPbk85+QNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMY8trO12aonoyYWcf\/m6p5bJdrtlUeltr7tq93166v+tmn59q275\/KZ\/zr9z8gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoCYzMLa7iWd3U5d36nFolW3fB\/TP5dT73f673L3607\/\/fK96ffp33DyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJivz+fswM+Nyzc7TP9cTi2OWTr7N\/9v1S2LYz6\/773t+bJqwvPZyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiHltYswz1M9OXjd72+Z1a\/lp16ne02\/T3ccvzatX0+2XVjQudTt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ8+v0Bew2fQHplsWiU6YvJZ1adnvbouEtr7v7cz61kHjqvp\/+3H3y\/nPyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJivz+euwa\/py2mnFp9uWRg69TlPXxy7Zblv+v0y\/Xc0\/T6Y\/vwrPe+dvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASAms7A2feFq1fSFplXTl5xWTV\/02m369a2a\/jyY\/vuYft9P\/34ncPIGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAmF9P\/eNTCzmnlo2mLyqdcsui0u7re9v7XfW25bnpz7Xppt\/3T97PTt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ8\/X5vHO459Si0qpT13fLMp7Ppfm6\/Mzu58bbnkOrJvTDyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBifp2+gN3etgh0y+uuOrUMZSHse9Pvl1XT38ep+\/Rtz8nS79zJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGK+Pp9nBoPetnS2ysLQLNMXs6Z\/b+7n7+2+vlsW6la5r\/47J28AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIOb6wtmr6gs8ppxaapi9Dnbpfpn8up\/j9fu\/U8tctn9+qCYtouzl5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMyvp\/7xLYs2p97H6gLS9OUli08\/M\/3+O\/W6p5bJTpn++91t+vc24fqcvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW1VdMXwlZNWNz5k+nXN33harrp3++q6b\/zVaeWzk59frfcf9Pvq985eQNAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0DM8YW1W9yyTHZqKWn6stH0Zai33X+rfG\/\/xi3vo8TJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGK+Pp9nhq12L3VNX+A65dSy0fTvY\/ry1y3cfxSc6syTvw8nbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIj59dQ\/PrW8dOp1V+1enjvllgWz6Utdu5ehTn1vp97H9N+b6\/uZ1eu7cfHTyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiHltYm774dMr0havdr7vb7vcx\/XM59f+mL\/ytmr78Nf3+u2UZb3oX\/oaTNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMQ8trA2fflm+oLU9EWg6Z\/fLaYvcJ163emLXrd42\/c2\/bn7OydvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHlsYe2W5ZvpizunFrimL3\/d8rmsmr6Etdv03+WqWxYmT90v0\/\/fk5y8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOaxhbXpy2m7nXq\/t3x+t7jl+9h9P59awjq19Dj989vtlvc7fenxd07eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEPPYwtrbloimL\/NM\/z4s1P3M2xYNp7+P6de326nny27Tr+93Tt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ89jC2m6nlm9OLZ2d+n+n3PK5TF9omr5AuPv6Tr3fU255\/p1aepy+5Pk7J28AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIOb6wdmoRaPV1py+YvW0Bbvr73W36MtSEpal\/afqC3qrpC2b8d07eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEHN8YW23U0tTq04tmJ16v6cW0VZf922LfLtNX+A6dX2n3u\/0Rb7d3rwY6OQNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMY8trE1fqpm+lLTqbctpu\/\/fqSW26b+PW7ztcz71HLrldUu\/cydvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHlsYW3VqWWe3XYv80xY8PmTU9c3fdlt+kLTqeW5Vad+R6umLy5Od8si5ARO3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABDz2MLa9AWp3d62qHTLstv0Ra9b7ufpC1fTl79uuQ\/e9px8kpM3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxDy2sDZ9sWj66+5e9Jq+cHXKqSWx6d\/b9EWv6ctk0+8D91+fkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEPLawZiHne6uLRacWpKYv1O1enrvF9MWsW0z\/vd2yjLdq+pLdk5y8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOaxhbVVpUWbP9m9bGQh7HvT75dT39stn8upZcFVp5YAp3+\/p65v+rLbk5y8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOb4wtqq6YtKp1731JLT7v83YbHoT07df29b2tvt1P28avqi3PT7b\/pz40lO3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABCTWVhjllPLRqeWsKY7tSR26v+tumU5bff1Tf8d7V6O3G3C88XJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGIsrA11y3LVbqfe76pTS1irpi9\/7Tb9+m4xfclu1annxt9w8gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoCYzMKapaSfObVsNH2p620LddNfd\/qC3qrpn\/Pu\/3fL86XUGSdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiDm+sDZ9KWm6U8tVuxeLdt8Htyx1TV+ueptbltNW7b7\/bllEm7Ac6eQNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMV+fjyElAChx8gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAmP8A35gKYsiGTCoAAAAASUVORK5CYII=",
// 							"url": ""
// 						}
// 				}
// 			}
// 		}
// 	*/
// 	/* // Success VA
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/virtual-account",
// 				"data": {
// 						"bankCode": "014",
// 						"singleUse": true,
// 						"type": "ClosedAmount",
// 						"reference": "1719307920",
// 						"amount": 15000,
// 						"expiryMinutes": 10,
// 						"viewName": "Testing",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						}
// 				},
// 				"headers": {
// 						"On-Key": "SC-KRW9ESNZUUKQXOOX",
// 						"On-Token": "jlaVHot4XGnCqYU8FI20GHwkv6RMOT2t",
// 						"On-Signature": "53de56668b239f4c9ced6e2cc1d6eab408b297267d9d06a22c5b622504a1f85b9cf974a45955d6e3a2dc7f3ac664022699c3f809c4683fe2196582963efd730f",
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
// 						"id": "4dd6daf1-50bc-4508-af26-e57da5bffbdf",
// 						"merchantRef": "1719307920",
// 						"status": "pending",
// 						"feePayer": "customer",
// 						"amount": 15000,
// 						"fee": 5000,
// 						"totalAmount": 20000,
// 						"expiredDate": "2024-06-25T16:42:01+07:00",
// 						"paidDate": null,
// 						"settleDate": "2024-06-25T16:32:01+07:00",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						},
// 						"virtualAccount": {
// 							"bankCode": "014",
// 							"vaNumber": "036709208150",
// 							"viewName": "Testing"
// 						}
// 				}
// 			}
// 		}
// 	*/
// 	/* // Success EWALLET
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/e-wallet",
// 				"data": {
// 						"reference": "1719307998",
// 						"phoneNumber": "081212121314",
// 						"channel": "ovo",
// 						"amount": 15000,
// 						"expiryMinutes": 10,
// 						"viewName": "Testing",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo",
// 							"successRedirectUrl": "http:\/\/tester.com\/secure\/callback\/demo"
// 						}
// 				},
// 				"headers": {
// 						"On-Key": "SC-KRW9ESNZUUKQXOOX",
// 						"On-Token": "jlaVHot4XGnCqYU8FI20GHwkv6RMOT2t",
// 						"On-Signature": "bb6739a98bb8b506750b17946a63d2178cbdc378e8a28fe07b6a3868b9039f7b76718fc7f5f9d2085d8984c3a8b4bb0a500fcd3ae6f4f04a720abf7bb08acb66",
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
// 						"id": "506ae055-a75a-4a0f-a562-8ff599611a62",
// 						"merchantRef": "1719307998",
// 						"status": "pending",
// 						"feePayer": "customer",
// 						"amount": 15000,
// 						"fee": 450,
// 						"totalAmount": 15450,
// 						"expiredDate": "2024-06-25T16:43:19+07:00",
// 						"paidDate": null,
// 						"settleDate": "2024-06-25T16:33:19+07:00",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						},
// 						"eWallet": {
// 							"viewName": "Testing",
// 							"channel": "ovo",
// 							"url": "http:\/\/www.wilderman.info\/atque-non-non-commodi-atque-praesentium-iusto-vero.html"
// 						}
// 				}
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
// 	$transaction->setParams([
// 		'id' => '4dd6daf1-50bc-4508-af26-e57da5bffbdf',
// 		'resend_callback' => 0,
// 	]);
// 	$result = $vendor->InquiryPayment($transaction);
// 	extract($result);
// 	print_r($response);
// 	/* // Success inquiry QRIS
// 		{
// 			"status": "000",
// 			"request": {
// 				"url": "https:\/\/api.sandbox.cronosengine.com\/api\/check\/4dd6daf1-50bc-4508-af26-e57da5bffbdf?resendCallback=0",
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
// 						"id": "4dd6daf1-50bc-4508-af26-e57da5bffbdf",
// 						"merchantRef": "1719307920",
// 						"status": "pending",
// 						"feePayer": "customer",
// 						"amount": 15000,
// 						"fee": 5000,
// 						"totalAmount": 20000,
// 						"expiredDate": "2024-06-25T16:42:01+07:00",
// 						"paidDate": null,
// 						"settleDate": "2024-06-25T16:32:01+07:00",
// 						"additionalInfo": {
// 							"callback": "http:\/\/tester.com\/secure\/callback\/demo"
// 						},
// 						"virtualAccount": {
// 							"bankCode": "014",
// 							"vaNumber": "036709208150",
// 							"viewName": "Testing"
// 						}
// 				}
// 			}
// 		}
// 	*/
// } catch (\Throwable $e) {
// 	print_r(ErrorToString($e)); // vendor->ThrowErrorException($e);
// }
