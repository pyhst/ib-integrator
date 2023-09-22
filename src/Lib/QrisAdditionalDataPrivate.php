<?php

namespace IbIntegrator\Lib;

class QrisAdditionalDataPrivate
{

	protected $debug = 0;

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Qris data objects
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	public $productIndicator = [
		'id' => 'PI',
		'value' => ''
	];
	public $customerData = [
		'id' => 'CD',
		'value' => ''
	];
	public $merchantCriteria = [
		'id' => 'MC',
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
			if (isset($value['value'])) { // To accomadate CD00 in AJ CustomerData // if (!empty($value['value'])) {
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
