<?php

$env = __DIR__ .'/../.env';
if (file_exists($env)) {
	$dotenv = new Symfony\Component\Dotenv\Dotenv();
	$dotenv->load($env);
}

define('IB_HELPER_APP_DEBUG', $_ENV['IB_APP_DEBUG'] ?? env('APP_DEBUG') ?? null);
define('IB_HELPER_TIMER_START', microtime(true));



/** /*------------------------------------------------------------------  // Strings   -------------------------------------------------------*
 *
 * Rupiah string
 *
 */
if (!function_exists('toRupiah')) {
	function toRupiah(string $amount): string
	{
		return 'Rp.' . number_format($amount, 0, ',', '.') . ',-';
	}
}



/** /*------------------------------------------------------------------  // URLs   -------------------------------------------------------*
 *
 * Clean URL
 *
 */
if (!function_exists('CleanURL')) {
	function CleanURL(string $url): string
	{
		return preg_replace('/([^:])(\/{2,})/', '$1/', $url);
	}
}
/**
 *
 * Check existence of URL
 *
 */
if (!function_exists('CheckURL')) {
	function CheckURL(string $url)
	{
		$headers = @get_headers($url);
		if (!empty($headers)) {
			if (strpos($headers[0], '200') !== false
				|| strpos($headers[0], '405') !== false
			) {
				return true;
			}
		}
		return false;
	}
}



/** /*------------------------------------------------------------------  // Encodings   -------------------------------------------------------*
 *
 * Check if base64_encoded
 *
 */
if (!function_exists('CheckBase64')) {
	function CheckBase64(string $data)
	{
		if (base64_encode(base64_decode($data, true)) === $data) {
			return true;
		}
		return false;
	}
}



/** /*------------------------------------------------------------------  // Arrays   -------------------------------------------------------*
 *
 * Lazy obj to arr
 *
 */
if (!function_exists('ObjectToArray')) {
	function ObjectToArray(object $object): array
	{
		return json_decode(json_encode($object), true);
	}
}
/**
 *
 * Find value by key in multidimension array
 *
 */
if (!function_exists('ArrayValueRecursive')) {
	function ArrayValueRecursive($key, array $arr)
	{
		$val = array();
		array_walk_recursive($arr, function ($v, $k) use ($key, &$val) {
			if ($k == $key) array_push($val, $v);
		});
		return count($val) > 1 ? $val : array_pop($val);
	}
}
/**
 *
 * Object to array recursize
 *
 */
if (!function_exists('ObjectToArrayRecursive')) {
	function ObjectToArrayRecursive($d)
	{
	   if (is_object($d)) $d = get_object_vars($d);
   	return is_array($d) ? array_map(__FUNCTION__, $d) : $d;
	}
}
/**
 *
 * Array to object recursize
 *
 */
if (!function_exists('ArrayToObjectRecursive')) {
	function ArrayToObjectRecursive($d)
	{
		return is_array($d) ? (object) array_map(__FUNCTION__, $d) : $d;
	}
}


/** /*------------------------------------------------------------------  // JSONs   -------------------------------------------------------*
 *
 * Check if is JSON
 *
 */
if (!function_exists('IsJSON')) {
	function IsJSON($string): bool
	{
		return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}
}
/**
 *
 * Pretty JSON
 *
 */
if (!function_exists('PrettyJSON')) {
	function PrettyJSON($string): string
	{
		if (!empty($string) && IsJSON($string)) {
			return json_encode(json_decode($string), JSON_PRETTY_PRINT);
		}
		return $string;
	}
}
/**
 *
 * Return response in JSON
 *
 */
if (!function_exists('JSONResult')) {
	function JSONResult($request, $response, $status_code = 200): array
	{
		return [
			'request' => (array) $request,
			'response' => [
				'content' => json_encode($response),
				'status_code' => $status_code,
			],
		];
	}
}



/** /*------------------------------------------------------------------  // Exceptions   -------------------------------------------------------*
 *
 * Inform Exception error string
 *
 */
if (!function_exists('ErrorToString')) {
	function ErrorToString(\Throwable $e, $context = null, $message = null): string
	{
		if ($e instanceof \Exception) {
			return implode(', ', array_filter([
				$e->getMessage(),
				$context,
				$message,
			]));
		}
		return '';
	}
}
if (!function_exists('ErrorToTrace')) {
	function ErrorToTrace(\Throwable $e): array
	{
		if ($e instanceof \Exception) {
			$errors = [];
			$limit = 5;
			$n = 0;
			foreach ($e->getTrace() as $each_trace) {
				$n++;
				$errors[] = implode('', array_filter([
					basename(dirname($each_trace['file'])) . '/' . basename($each_trace['file']),
					"->" . $each_trace['function'] . "()",
					':' . $each_trace['line'],
					// ', ' . $e->getMessage(),
				]));
				if ($n == $limit) {
					break;
				}
			}
			return $errors;
		}
		return [];
	}
}
/**
 *
 * Return throwable Error Res
 *
 */
if (!function_exists('ErrorToResult')) {
	function ErrorToResult(\Throwable $e, $context = null, $message = null, $timer_start = IB_HELPER_TIMER_START): array
	{
		if (IB_HELPER_APP_DEBUG == true) {
			$debug = [
				'_debug' => [
					'error_message' => ErrorToString($e, $context, $message),
					'error_trace' => ErrorToTrace($e),
				]
				+ ['execution_time_ms' => round((microtime(true) - $timer_start) * 1000, 2)]
				// + ['error_trace_c' => $e->getTrace()]
			];
		}
		return [
			'status' => strval($e->getCode()) == '0' ? 999 : str_pad($e->getCode(), 3, '0', STR_PAD_LEFT),
			'message' => $e->getMessage(),
		] + ($debug ?? []);
	}
}
/**
 *
 * Throw Exception
 *
 */
if (!function_exists('ThrowErrorException')) {
	function ThrowErrorException(\Throwable $e, $context = null, $message = null)
	{
		$n = 1;
		$error = "[1] " . ErrorToString($e, $context, $message);
		while($e = $e->getPrevious()) {
			$n++;
			$error .= " [$n] " . ErrorToString($e);
		}
		throw new \Exception($error);
	}
}
/**
 *
 * Argument validator
 *
 */
if (!function_exists('ValidateArgs')) {
	function ValidateArgs(object $object, array $array)
	{
		foreach ($array as $a) {
			if (!isset($object->{$a}) || empty($object->{$a})) {
				throw new InvalidArgumentException('Missing argument ' . $a);
			}
		}
	}
}



/** /*------------------------------------------------------------------  // IPs   -------------------------------------------------------*
 *
 * Check if IP is in range
 *
 */
if (!function_exists('IfIPInRange')) {
	function IfIPInRange(string $ip, string $range): bool
	{
		if (is_string($ip) && is_string($range)) {
			if (strpos($range, '/') == false) {
				$range .= '/32';
			}
			// $range is in IP/CIDR format eg 127.0.0.1/24
			list($range, $netmask) = explode('/', $range, 2);
			$range_decimal = ip2long($range);
			$ip_decimal = ip2long($ip);
			$wildcard_decimal = pow(2, (32 - $netmask)) - 1;
			$netmask_decimal = ~$wildcard_decimal;
			return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
		}
	}
}



/** /*------------------------------------------------------------------  // Dates   -------------------------------------------------------*
 *
 * Turns Ym date into code
 *
 */
if (!function_exists('YMDateToCode')) {
	function YMDateToCode($timestamp_or_stringtoreverse = null, $reverse = 0, $base_year = 2020)
	{
		if (!$reverse) {
			// Get year
			$time = \Carbon\Carbon::createFromTimestamp($timestamp_or_stringtoreverse);
			$y = (int) ($time->year - $base_year);
			$ya = NumberToAlphabet($y);
			$yb = str_pad($ya, 2, '0', STR_PAD_LEFT);
			// Get month
			$ma = NumberToAlphabet($time->month);
			return [
				'timestamp_or_stringtoreverse' => $timestamp_or_stringtoreverse,
				'origin' => $time->format('Ym'),
				'formatted' => $yb . $ma,
			];
			// return $yb . $ma;
		} else {
			// Reverse
			$s = substr($timestamp_or_stringtoreverse, 0, 3); // Get first 3 digit
			$y = substr($s, 0, 2); // First 2 digit of 3 is Y
			$m = substr($s, -1, 1); // Last 1 digit of 3 is M
			$a = substr($timestamp_or_stringtoreverse, 3, 999); // Get additional string codes
			try {
				$d = \Carbon\Carbon::createFromFormat('Ym',
					(int) (AlphabetToNumber( preg_replace('/[^a-z]/i','', $y) ) + $base_year) .
					str_pad(AlphabetToNumber($m), 2, 0, STR_PAD_LEFT)
				);
			} catch (\Throwable $e) {
				return [
					'timestamp_or_stringtoreverse' => null,
					'origin' => null,
					'adds' => null,
					'formatted' => null,
				];
			}
			return [
				'timestamp_or_stringtoreverse' => $timestamp_or_stringtoreverse,
				'origin' => $d->format('Ym'),
				// 's' => $s,
				// 'y' => $y,
				// 'm' => $m,
				'adds' => $a,
				'formatted' => $timestamp_or_stringtoreverse,
			];
			// return $d->format('Ym');
		}
	}
}
/**
 *
 * Translate number into alphabet
 *
 */
if (!function_exists('NumberToAlphabet')) {
	function NumberToAlphabet($number)
	{
		$number = intval($number);
		if ($number <= 0) {
			return '';
		}
		$alphabet = '';
		while ($number != 0) {
			$p = ($number - 1) % 26;
			$number = intval(($number - $p) / 26);
			$alphabet = chr(65 + $p) . $alphabet;
		}
		return $alphabet;
	}
}
/**
 *
 * Translate alphabet into number
 *
 */
if (!function_exists('AlphabetToNumber')) {
	function AlphabetToNumber($string)
	{
		$string = strtoupper($string);
		$length = strlen($string);
		$number = 0;
		$level = 1;
		while ($length >= $level) {
			$char = $string[$length - $level];
			$c = ord($char) - 64;
			$number += $c * (26 ** ($level - 1));
			$level++;
		}
		return $number;
	}
}



/** /*------------------------------------------------------------------  // Luhns   -------------------------------------------------------*
 *
 * Luhn
 *
 */
if (!function_exists('CalculateLuhn')) {
	function CalculateLuhn($number)
	{
		$sumTable = array(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), array(0, 2, 4, 6, 8, 1, 3, 5, 7, 9));
		$length = strlen($number);
		$sum = 0;
		$flip = 1;
		for ($i = $length-1; $i >= 0; --$i) {
			$sum += $sumTable[$flip++ & 0x1][$number[$i]]; // Sum digits (last one is check digit, which is not in parameter)
		}
		$sum *= 9; // Multiply by 9
		return (int)substr($sum, -1, 1); // Last digit of sum is check digit
	}
}
if (!function_exists('ValidateLuhn')) {
	function ValidateLuhn(string $number): bool
	{
		$sum = 0;
		$flag = 0;
		for ($i = strlen($number) - 1; $i >= 0; $i--) {
			$add = $flag++ & 1 ? $number[$i] * 2 : $number[$i];
			$sum += $add > 9 ? $add - 9 : $add;
		}
		return $sum % 10 === 0;
	}
}
if (!function_exists('CheckThenAddLuhn')) {
	function CheckThenAddLuhn($number)
	{
		if (isset($number) && !ValidateLuhn($number)) {
			return $number . CalculateLuhn($number);
		}
		return $number;
	}
}
if (!function_exists('CheckThenRemoveLuhn')) {
	function CheckThenRemoveLuhn($number)
	{
		if (isset($number) && ValidateLuhn($number)) {
			return substr($number, 0, (strlen($number) - 1));
		}
		return $number;
	}
}
