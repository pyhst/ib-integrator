<?php

namespace IbIntegrator\Lib;

/**
 *
 * MerchantAccountInfo = Qris ID Root # 02-51
 *
 */
class QrisMerchantAccountInfo
{

	protected $debug = 0;

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Qris data objects
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public $merchantPanOnlyString = [
		'id' => '',
		'value' => ''
	];
	public $globallyUniqueIdentifier = [
		'id' => '00',
		'value' => ''
	];
	public $merchantPan = [
		'id' => '01',
		'value' => ''
	];
	public $merchantId = [
		'id' => '02',
		'value' => ''
	];
	public $merchantCriteria = [
		'id' => '03',
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
		$str_mai = '';
		foreach($this as $key => $value) {
			if (!empty($value['value'])) {
				$str_mai .=
					($this->debug ? $key : '')  .
					$value['id'] .
					str_pad(strlen(   $value['value']   ), 2, '0', STR_PAD_LEFT) .
					$value['value'] .
					($this->debug ? ' >> ' : '')  .
					'';
			}
		}
		return $str_mai;
	}

}