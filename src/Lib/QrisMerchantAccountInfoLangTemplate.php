<?php

namespace IbIntegrator\Lib;

/**
 *
 * MerchantAccountInfoLangTemplate = Qris ID Root # 64
 *
 */
class QrisMerchantAccountInfoLangTemplate
{

	protected $debug = 0;

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Qris data objects
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public $langPreference = [
		'id' => '00',
		'value' => ''
	];
	public $merchantNameAltLang = [
		'id' => '01',
		'value' => ''
	];
	public $merchantCityAltLang = [
		'id' => '02',
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