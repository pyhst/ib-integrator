<?php

use Essefx\IbIntegrator\Lib\Qris;
use Essefx\IbIntegrator\Lib\QrisMerchantAccountInfo;
use Essefx\IbIntegrator\Lib\QrisAdditionalDataField;

require_once __DIR__ . '/../includes.php';

$qris = new Qris;

/*--------------------------------------  // Qris root data object  -------------------------------------------------------*/
$qris->set($qris->payloadFormatIndicator, '01');
$qris->set($qris->pointOfInitiationMethod, '12');
//
$qris->set($qris->transactionAmount, ((int) 100000) . '.00');
$qris->set($qris->transactionCurrency, '360');
//
// $qris->set($qris->tipOrConvenienceIndicator, '01'); // Manual tips
// $qris->set($qris->tipOrConvenienceIndicator, '02'); // Fixed amount tips
// 	$qris->set($qris->valueOfConvenienceFeeFixed, ((int) 100000) . '.00');
// $qris->set($qris->tipOrConvenienceIndicator, '03'); // Percentage amount tips
// 	$qris->set($qris->valueOfConvenienceFeePercentage, (int) 10);
//
$qris->set($qris->merchantCategoryCode, '1234');
$qris->set($qris->countryCode, 'ID');
$qris->set($qris->merchantName, 'MERCHANT TESTER');
$qris->set($qris->postalCode, '12345');
$qris->set($qris->merchantCity, 'JAKARTA');

/*--------------------------------------  // Qris MAI 26  -------------------------------------------------------*/
$mai26 = new QrisMerchantAccountInfo();
$mai26->Set($mai26->globallyUniqueIdentifier, 'COM.ESSEFX.WWW');
$mai26->Set($mai26->merchantPan, '93600836');
$mai26->Set($mai26->merchantId, '1234567890');
$mai26->Set($mai26->merchantCriteria, 'UMI');
$qris->set($qris->merchantAccountInformation26, $mai26->Encode());

/*--------------------------------------  // Additional data  -------------------------------------------------------*/
$add = new QrisAdditionalDataField();
$add->Set($add->billNumber, 'QR10001');
$add->Set($add->mobileNumber, '081212121314');
$add->Set($add->storeLabel, 'CGK');
$add->Set($add->loyaltyNumber, 'SILVER');
$add->Set($add->referenceLabel, 'INV20230922');
$add->Set($add->customerLabel, null);
$add->Set($add->terminalLabel, 'A01');
$add->Set($add->purposeOfTransaction, null);
$add->Set($add->additionalConsumerData, null);
$qris->set($qris->additionalDataField, $add->Encode());

$compiled = $qris->CompileQris();
exit( $compiled );