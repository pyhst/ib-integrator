<?php

namespace Essefx\IbIntegrator\Lib;

class QrisAdditionalDataField
{

	protected $debug = 0;

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Qris data objects - AdditionalDataField = Qris ID Root # 62
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public $billNumber = [
		'id' => '01',
		'value' => ''
	];
	public $mobileNumber = [
		'id' => '02',
		'value' => ''
	];
	public $storeLabel = [
		'id' => '03',
		'value' => ''
	];
	public $loyaltyNumber = [
		'id' => '04',
		'value' => ''
	];
	public $referenceLabel = [
		'id' => '05',
		'value' => ''
	];
	public $customerLabel = [
		'id' => '06',
		'value' => ''
	];
	public $terminalLabel = [
		'id' => '07',
		'value' => ''
	];
	public $purposeOfTransaction = [
		'id' => '08',
		'value' => ''
	];
	public $additionalConsumerData = [
		'id' => '09',
		'value' => ''
	];



	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Functions
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public function Set(&$prop, $value)
	{
		$prop['value'] = $value;
	}
	public function Get($prop)
	{
		return $prop['value'];
	}

	public function Encode()
	{
		$str_add = '';
		foreach($this as $key => $value) {
			if (!empty($value['value'])) {
				$str_add .=
					($this->debug ? $key : '')  .
					$value['id'] .
					str_pad(strlen(   $value['value']   ), 2, '0', STR_PAD_LEFT) .
					$value['value'] .
					($this->debug ? '&tab;' : '')  .
					'';
			}
		}
		return $str_add;
	}

}
