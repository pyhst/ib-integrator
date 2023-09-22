<?php

use IbIntegrator\Lib\Qris;

require_once __DIR__ . '/../includes.php';

$qris = new Qris;

$qris_string = '00020101021226610014COM.ESSEFX.WWW0118936008360000000000021012345678900303UMI510702031235204123453033605409100000.005502025609100000.005802ID5915MERCHANT TESTER6007JAKARTA61051234562660107QR1000102120812121213140303CGK0406SILVER0511INV202309220703A016304C5D2';
$decode = $qris->DecompileQris($qris_string, new Qris, true);
$verify = $qris->VerifyCRC($decode);

echo '<pre>';
print_r([
	$decode,
	$verify,
]);
echo '</pre>';
exit();
