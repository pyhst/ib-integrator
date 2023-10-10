<?php


/**
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



/**
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
		if (!empty($headers) && strpos($headers[0], '200')) {
			return true;
		}
		return false;
	}
}



/**
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

/**
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
 * Check if is JSON
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



/**
 *
 * Inform Exception error string
 *
 */
if (!function_exists('ErrorString')) {
	function ErrorString(\Throwable $e, $context = null, $message = null): string
	{
		if ($e instanceof \Exception) {
			return implode('', [
				basename(dirname($e->getFile())) . '/' .
				basename($e->getFile()),
				"->" . ($context ?? $e->getTrace()[0]['function']),
				':' . $e->getLine(),
			]) .
			($message ? ', ' . $message : null) .
			($e->getMessage() ? ', ' . $e->getMessage() : null);
		}
		return $e->getMessage();
	}
}

/**
 *
 * Return throwable Error Res
 *
 */
if (!function_exists('ErrorResult')) {
	function ErrorResult(\Throwable $e, $context = null, $message = null, $timer_start = null): array
	{
		if (
			(!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] == 'true')
		) {
			$debug = [
				'_debug' => [
					'error_message' => ErrorString($e, $context ? $context . '()' : '', $message),
				] + ($timer_start ? ['execution_time_ms' => round((microtime(true) - $timer_start) * 1000, 2)] : []),
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
		$error = "[1] " . ErrorString($e, $context, $message);
		while($e = $e->getPrevious()) {
			$n++;
			$error .= " [$n] " . ErrorString($e);
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



/**
 *
 * Luhn
 *
 */
if (!function_exists('CheckLuhn')) {
	function CheckLuhn($card, $create = false)
	{
		$segments = str_split($card, 15);
		$digits = str_split($segments[0], 1);
		foreach ($digits as $k => $d) {
			if ($k % 2 == 0) {
				$digits[$k] *= 2;
				if (strlen($digits[$k]) > 1) {
					$split = str_split($digits[$k]);
					$digits[$k] = array_sum($split);
				}
			}
		}
		$digits = array_sum($digits) * 9;
		$digits = str_split($digits);
		$checksum = $digits[max(array_keys($digits))];
		if ($create == false) {
			if (!isset($segments[1])) {
				return "Invalid input length.";
			}
			if ($checksum == $segments[1]) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return $segments[0] . $checksum;
		}
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

if (!function_exists('CheckThenAddLuhn')) {
	function CheckThenAddLuhn($number)
	{
		if (!ValidateLuhn($number)) {
			return $number . CalculateLuhn($number);
		}
		return $number;
	}
}
