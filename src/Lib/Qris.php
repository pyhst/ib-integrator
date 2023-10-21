<?php

namespace IbIntegrator\Lib;

use IbIntegrator\Lib\Crc16;

class Qris
{

	protected $debug = 0;

	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Qris data objects
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	/**
	 *
	 * Qris defaul version, currently 01 (as per Sept 09' 2023)
	 *
	 */
	public $payloadFormatIndicator = [
		'id' => '00',
		'value' => '01'
	];

	/**
	 *
	 * 11 for Static Qris, 12 for Dynamic Qris
	 *
	 */
	public $pointOfInitiationMethod = [
		'id' => '01',
		'value' => '12'
	];

	/**
	 *
	 * MAI template reserved for Visa 02 & 03
	 * 02 - 03
	 *
	 */
	public $merchantAccountInformation02 = [
		'id' => '02',
		'value' => ''
	];
	public $merchantAccountInformation03 = [
		'id' => '03',
		'value' => ''
	];

	/**
	 *
	 * MAI template reserved for Mastercard 04 & 05
	 * 04 - 05
	 *
	 */
	public $merchantAccountInformation04 = [
		'id' => '04',
		'value' => ''
	];
	public $merchantAccountInformation05 = [
		'id' => '05',
		'value' => ''
	];

	/**
	 *
	 * MAI template reserved for EMVCo
	 *	06 - 08
	 *
	 */
	public $merchantAccountInformation06 = [
		'id' => '06',
		'value' => ''
	];
	public $merchantAccountInformation07 = [
		'id' => '07',
		'value' => ''
	];
	public $merchantAccountInformation08 = [
		'id' => '08',
		'value' => ''
	];

	/**
	 *
	 * MAI template reserved for Discover
	 *	09 - 10
	 *
	 */
	public $merchantAccountInformation09 = [
		'id' => '09',
		'value' => ''
	];
	public $merchantAccountInformation10 = [
		'id' => '10',
		'value' => ''
	];

	/**
	 *
	 * MAI template reserved for Amex
	 * 11 - 12
	 *
	 */
	public $merchantAccountInformation11 = [
		'id' => '11',
		'value' => ''
	];
	public $merchantAccountInformation12 = [
		'id' => '12',
		'value' => ''
	];

	/**
	 *
	 * MAI template reserved for JCB
	 * 13 - 14
	 *
	 */
	public $merchantAccountInformation13 = [
		'id' => '13',
		'value' => ''
	];
	public $merchantAccountInformation14 = [
		'id' => '14',
		'value' => ''
	];

	/**
	 *
	 * MAI template reserved for UnionPay
	 * 15 - 16
	 *
	 */
	public $merchantAccountInformation15 = [
		'id' => '15',
		'value' => ''
	];
	public $merchantAccountInformation16 = [
		'id' => '16',
		'value' => ''
	];

	/**
	 *
	 * MAI template reserved for EMVCo
	 * 17 - 25
	 *
	 */
	public $merchantAccountInformation17 = [
		'id' => '17',
		'value' => ''
	];
	public $merchantAccountInformation18 = [
		'id' => '18',
		'value' => ''
	];
	public $merchantAccountInformation19 = [
		'id' => '19',
		'value' => ''
	];
	public $merchantAccountInformation20 = [
		'id' => '20',
		'value' => ''
	];
	public $merchantAccountInformation21 = [
		'id' => '21',
		'value' => ''
	];
	public $merchantAccountInformation22 = [
		'id' => '22',
		'value' => ''
	];
	public $merchantAccountInformation23 = [
		'id' => '23',
		'value' => ''
	];
	public $merchantAccountInformation24 = [
		'id' => '24',
		'value' => ''
	];
	public $merchantAccountInformation25 = [
		'id' => '25',
		'value' => ''
	];

	/**
	 *
	 * MAI reserved for domestic payment network
	 *	26 - 45
	 * Wajib diisi Acquirer Domestik
	 *
	 */
	public $merchantAccountInformation26 = [
		'id' => '26',
		'value' => ''
	];
	public $merchantAccountInformation27 = [
		'id' => '27',
		'value' => ''
	];
	public $merchantAccountInformation28 = [
		'id' => '28',
		'value' => ''
	];
	public $merchantAccountInformation29 = [
		'id' => '29',
		'value' => ''
	];
	public $merchantAccountInformation30 = [
		'id' => '30',
		'value' => ''
	];
	public $merchantAccountInformation31 = [
		'id' => '31',
		'value' => ''
	];
	public $merchantAccountInformation32 = [
		'id' => '32',
		'value' => ''
	];
	public $merchantAccountInformation33 = [
		'id' => '33',
		'value' => ''
	];
	public $merchantAccountInformation34 = [
		'id' => '34',
		'value' => ''
	];
	public $merchantAccountInformation35 = [
		'id' => '35',
		'value' => ''
	];
	public $merchantAccountInformation36 = [
		'id' => '36',
		'value' => ''
	];
	public $merchantAccountInformation37 = [
		'id' => '37',
		'value' => ''
	];
	public $merchantAccountInformation38 = [
		'id' => '38',
		'value' => ''
	];
	public $merchantAccountInformation39 = [
		'id' => '39',
		'value' => ''
	];
	public $merchantAccountInformation40 = [
		'id' => '40',
		'value' => ''
	];
	public $merchantAccountInformation41 = [
		'id' => '41',
		'value' => ''
	];
	public $merchantAccountInformation42 = [
		'id' => '42',
		'value' => ''
	];
	public $merchantAccountInformation43 = [
		'id' => '43',
		'value' => ''
	];
	public $merchantAccountInformation44 = [
		'id' => '44',
		'value' => ''
	];
	public $merchantAccountInformation45 = [
		'id' => '45',
		'value' => ''
	];

	/**
	 *
	 * MAI reserved for domestic ID
	 * 46 - 50
	 *
	 */
	public $merchantAccountInformation46 = [
		'id' => '46',
		'value' => ''
	];
	public $merchantAccountInformation47 = [
		'id' => '47',
		'value' => ''
	];
	public $merchantAccountInformation48 = [
		'id' => '48',
		'value' => ''
	];
	public $merchantAccountInformation49 = [
		'id' => '49',
		'value' => ''
	];
	public $merchantAccountInformation50 = [
		'id' => '50',
		'value' => ''
	];

	/**
	 *
	 * MAI reserved Domestic Merchant Repository
	 *
	 */
	public $merchantAccountInformation51 = [
		'id' => '51',
		'value' => ''
	];

	/**
	 *
	 * Other qris data object
	 *
	 */
	public $merchantCategoryCode = [
		'id' => '52',
		'value' => ''
	];
	public $transactionCurrency = [
		'id' => '53',
		'value' => ''
	];
	public $transactionAmount = [
		'id' => '54',
		'value' => ''
	];
	public $tipOrConvenienceIndicator = [
		'id' => '55',
		'value' => ''
	];
	public $valueOfConvenienceFeeFixed = [
		'id' => '56',
		'value' => ''
	];
	public $valueOfConvenienceFeePercentage = [
		'id' => '57',
		'value' => ''
	];
	public $countryCode = [
		'id' => '58',
		'value' => ''
	];
	public $merchantName = [
		'id' => '59',
		'value' => ''
	];
	public $merchantCity = [
		'id' => '60',
		'value' => ''
	];
	public $postalCode = [
		'id' => '61',
		'value' => ''
	];
	public $additionalDataField = [
		'id' => '62',
		'value' => ''
	];
	public $CRC = [
		'id' => '63',
		'value' => ''
	];
	public $merchantInfoLangTemplate = [
		'id' => '64',
		'value' => ''
	];

	/**
	 *
	 * RFU for EMVCo
	 * 65 - 79
	 *
	 */
	public $rfuForEmvCo65 = [
		'id' => '65',
		'value' => ''
	];
	public $rfuForEmvCo66 = [
		'id' => '66',
		'value' => ''
	];
	public $rfuForEmvCo67 = [
		'id' => '67',
		'value' => ''
	];
	public $rfuForEmvCo68 = [
		'id' => '68',
		'value' => ''
	];
	public $rfuForEmvCo69 = [
		'id' => '69',
		'value' => ''
	];
	public $rfuForEmvCo70 = [
		'id' => '70',
		'value' => ''
	];
	public $rfuForEmvCo71 = [
		'id' => '71',
		'value' => ''
	];
	public $rfuForEmvCo72 = [
		'id' => '72',
		'value' => ''
	];
	public $rfuForEmvCo73 = [
		'id' => '73',
		'value' => ''
	];
	public $rfuForEmvCo74 = [
		'id' => '74',
		'value' => ''
	];
	public $rfuForEmvCo75 = [
		'id' => '75',
		'value' => ''
	];
	public $rfuForEmvCo76 = [
		'id' => '76',
		'value' => ''
	];
	public $rfuForEmvCo77 = [
		'id' => '77',
		'value' => ''
	];
	public $rfuForEmvCo78 = [
		'id' => '78',
		'value' => ''
	];
	public $rfuForEmvCo79 = [
		'id' => '79',
		'value' => ''
	];

	/**
	 *
	 * Unreserved Templates
	 *	80 - 99
	 *
	 */
	public $unreservedTemplate80 = [
		'id' => '80',
		'value' => ''
	];
	public $unreservedTemplate81 = [
		'id' => '81',
		'value' => ''
	];
	public $unreservedTemplate82 = [
		'id' => '82',
		'value' => ''
	];
	public $unreservedTemplate83 = [
		'id' => '83',
		'value' => ''
	];
	public $unreservedTemplate84 = [
		'id' => '84',
		'value' => ''
	];
	public $unreservedTemplate85 = [
		'id' => '85',
		'value' => ''
	];
	public $unreservedTemplate86 = [
		'id' => '86',
		'value' => ''
	];
	public $unreservedTemplate87 = [
		'id' => '87',
		'value' => ''
	];
	public $unreservedTemplate88 = [
		'id' => '88',
		'value' => ''
	];
	public $unreservedTemplate89 = [
		'id' => '89',
		'value' => ''
	];
	public $unreservedTemplate90 = [
		'id' => '90',
		'value' => ''
	];
	public $unreservedTemplate91 = [
		'id' => '91',
		'value' => ''
	];
	public $unreservedTemplate92 = [
		'id' => '92',
		'value' => ''
	];
	public $unreservedTemplate93 = [
		'id' => '93',
		'value' => ''
	];
	public $unreservedTemplate94 = [
		'id' => '94',
		'value' => ''
	];
	public $unreservedTemplate95 = [
		'id' => '95',
		'value' => ''
	];
	public $unreservedTemplate96 = [
		'id' => '96',
		'value' => ''
	];
	public $unreservedTemplate97 = [
		'id' => '97',
		'value' => ''
	];
	public $unreservedTemplate98 = [
		'id' => '98',
		'value' => ''
	];
	public $unreservedTemplate99 = [
		'id' => '99',
		'value' => ''
	];



	/*\\\\\\\\\\\\\\\\\\\\\\\\ //////////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\
	|
	| Functions
	|
	///////////////////////////////////////////////////////////////// \\\\\\\\\\\\\\\\\\\\\\\\\ ///////////////////////*/

	protected $encode_attempt = 10;

	public function Set(&$prop, $value)
	{
		$prop['value'] = $value;
	}
	public function Get($prop)
	{
		return $prop['value'];
	}

	//

	public function CompileQris()
	{
		return $this->Encode();
	}
	public function DecompileQris($str, $ob_class, $sort = false)
	{
		return $this->Decode($str, $ob_class, $sort);
	}

	//

	public function Encode()
	{
		$qris_string = '';
		foreach ($this as $key => $value) {
			if (!empty($value['value'])) {
				$qris_string .=
					($this->debug ? $key : '')  .
					$value['id'] .
					str_pad(strlen($value['value']), 2, '0', STR_PAD_LEFT) .
					$value['value'] .
					($this->debug ? ' > ' : '')  .
					'';
			}
		}
		$debug_citt_attempts = [];
		//
		for ($i = 0; $i < $this->encode_attempt; $i++) {
			$citt = strtoupper(dechex((new Crc16)->CCITT_FALSE($qris_string . '6304')));
			$debug_citt_attempts[] = $citt;
			if ($i >= $this->encode_attempt) {
				$debug_arr = [
					'qris_string' => $qris_string,
					'debug_citt_attempts' => $debug_citt_attempts,
				];
				if ($this->debug) {
					throw new \Exception("Failed to generate 4 digit CRC in $this->encode_attempt attempts. Debug: " . json_encode($debug_arr), 900);
				}
				throw new \Exception("Failed to generate 4 digit CRC", 900);
			}
			if (strlen($citt) == 4) {
				break;
			}
		}
		// Append CRC
		$qris_string .= '6304' . $citt;
		$this->CRC['value'] = $citt;
		//
		return $qris_string;
	}

	public function Decode($str, $ob_class, $sort = false)
	{
		try {
			$id_roots = [];
			foreach ($ob_class as $key => $value) {
				if (is_array($value) && !empty($value['id'])) {
					$id_roots[$value['id']] = [
						'id' => $value['id'],
						'name' => $key,
						'value' => '',
						'searched' => 0,
					];
				}
			}
			//
			$arr_qris = [];
			$str_remains = $str;
			//
			for ($i = 0; $i < strlen($str); $i++) {
				$id_root = substr($str_remains, 0, 2);
				$pos_len = substr($str_remains, 2, 2);
				$fetch = substr($str_remains, 4, (int) $pos_len);
				$fetch_str = $id_root . $pos_len . $fetch;
				$fetch_array = [];
				$str_remains = preg_replace('/' . $fetch_str . '/', '', $str_remains, 1);
				if (
					!empty($id_roots[$id_root])
					&& $id_roots[$id_root]['searched'] == 0 // Avoid double assignment
				) {
					switch ($id_root) {
						case '01':
						case '02':
						case '03':
						case '04':
						case '05':
						case '06':
						case '07':
						case '08':
						case '09':
						case '10':
						case '11':
						case '12':
						case '13':
						case '14':
						case '15':
						case '16':
						case '17':
						case '18':
						case '19':
						case '20':
						case '21':
						case '22':
						case '23':
						case '24':
						case '25':
						case '26':
						case '27':
						case '28':
						case '29':
						case '30':
						case '31':
						case '32':
						case '33':
						case '34':
						case '35':
						case '36':
						case '37':
						case '38':
						case '39':
						case '40':
						case '41':
						case '42':
						case '43':
						case '44':
						case '45':
						case '46':
						case '47':
						case '48':
						case '49':
						case '50':
						case '51':
							$fetch_array = $this->Decode($fetch, new QrisMerchantAccountInfo());
							break;
						case '62':
							$fetch_array = $this->Decode($fetch, new QrisAdditionalDataField());
							break;
						case '64':
							$fetch_array = $this->Decode($fetch, new QrisMerchantAccountInfoLangTemplate());
							break;
					}
					$item = [
						'id_root' => $id_root,
						'id_root_name' => $id_roots[$id_root]['name'],
						'fetch_data' => $fetch,
						'fetch_length' => $pos_len,
						'fetch_string' => $fetch_str,
						'fetch_array' => $fetch_array,
						// 'str_remains' => $str_remains,
					];
					$arr_qris[$id_root] = $item;
					$id_roots[$id_root]['searched'] = 1;
				}
			}
			if ($sort) {
				asort($arr_qris);
			}
		} catch (\Throwable $e) {
			return StringError($e, __FUNCTION__);
		}
		return $arr_qris;
	}

	public function VerifyCRC($qris_arr)
	{
		$qris_string = '';
		$id_root_63 = '';
		foreach ($qris_arr as $arr) {
			if ($arr['id_root'] != '63') {
				$qris_string .= $arr['fetch_string'];
			} else {
				$id_root_63 = $arr['data'];
			}
		}
		$citt = strtoupper(dechex((new Crc16)->CCITT_FALSE($qris_string . '6304')));
		if (strlen($citt) == 3) {
			$citt = '0' . $citt;
		}
		$qris_string .= '6304' . $citt;
		return [
			'qrstr_new' => $qris_string,
			'qrstr_idroot63' => $id_root_63,
			'qrstr_new_ccitt' => $citt,
			'qrstr_strcmp' => strcmp($id_root_63, $citt),
		];
	}

}
