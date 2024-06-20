<?php

use IbIntegrator\Vendors\PaymentGateway\PayOk;
use IbIntegrator\Vendors\Transaction;

require_once __DIR__ . '/../includes.php';

$vendor = new PayOk();
$vendor->setID($_ENV['PAYOK_MERCHANT_ID']);
$vendor->setPrivateKey($_ENV['PAYOK_PRIVATE_KEY']);
$vendor->setHostURL($_ENV['PAYOK_HOST_URL_BILLING']);
$vendor->setPaymentURL($_ENV['PAYOK_HOST_URL_BILLING']);
$vendor->setRequestURL($_ENV['PAYOK_HOST_URL_BILLING']);
$vendor->setCallbackURL('http://sb.tf2us.com/secure/callback/demo');



/**
 *
 * Create billing
 *
 */
try {
	$transaction = new Transaction();
	$transaction->setAmount(15000);
	$transaction->setReferenceNumber(time());
	$transaction->setDescription('BILLING1');
	$transaction->setCustomerName('TESTER');
	$transaction->setCustomerID('1234567890');
	$transaction->setCustomerEmail('tester@gmail.com');
	$transaction->setCustomerCity('X');
	$transaction->setCustomerAddress('X');
	$transaction->setCustomerPhone('0811111111111');
	//
	/* // Channel list:
		Virtual Account:
			BNIVA
			CIMBVA
			PermataVA
			BRIVA
			MandiriVA
			BCAVA
			BSIVA
			BNCVA
		Alfamart:
			ConvenienceStore
		E-Wallet:
			DANAWALLET
			OVOWALLET
			SHOPEEPAY-APP(For Mobile App Users)
			SHOPEEPAY-WEB(Not Support)
			LINKAJA-APP(For Mobile App Users)
			LINKAJA-WEB(For Mobile APP/Desktop Users)
		QRIS:
			QRIS
	*/
	$transaction->setPaymentMethod('QRIS');
	$result = $vendor->CreateBilling($transaction);
	extract($result);
	print_r($response);
	/* // Success VA
		{
			"status": "000",
			"data": {
				"amount": 15000,
				"code": "SUCCESS",
				"countryCode": "IDN",
				"createTime": "20240609221751",
				"currency": "RP",
				"merchantId": "910061",
				"merchantOrderId": "1717946271",
				"paymentInfo": {
						"content": "3821005989401128",
						"expiredTime": "20240609224151",
						"type": "code"
				},
				"paymentMethodCode": "BNIVA",
				"platformOrderId": "2024060906100000005",
				"status": "PENDING",
				"updateTime": "20240609221751"
			}
		}
	*/
	/* // Success QRIS
		{
			"status": "000",
			"data": {
				"amount": 15000,
				"code": "SUCCESS",
				"countryCode": "IDN",
				"createTime": "20240609222330",
				"currency": "RP",
				"merchantId": "910061",
				"merchantOrderId": "1717946609",
				"paymentInfo": {
						"content": "5551741002874506",
						"expiredTime": "20240609224730",
						"type": "code"
				},
				"paymentMethodCode": "QRIS",
				"platformOrderId": "2024060906100000006",
				"status": "PENDING",
				"updateTime": "20240609222330"
			}
		}
	*/
} catch (\Throwable $e) {
	$vendor->ThrowErrorException($e);
}