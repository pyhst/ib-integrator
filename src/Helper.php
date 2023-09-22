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
if (!function_exists('StringError')) {
	function StringError(\Throwable $e, $context = null): string
	{
		if ($e instanceof \Exception) {
			return implode('', [
				basename($e->getFile()),
				"->" . $context,
				':' . $e->getLine(),
				', ' . $e->getMessage(),
			]);
		}
		return $e;
	}
}

/**
 *
 * Return throwable Error Res
 *
 */
if (!function_exists('ErrorResult')) {
	function ErrorResult(\Throwable $e, $context = null): array
	{
		if (
			(!empty(env('APP_DEBUG')) && env('APP_DEBUG') == true)
			|| (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] == 'true')
		) {
			$debug = [
				'_debug_error' => StringError($e, $context ? $context . '()' : ''),
				'_debug_timer' => round((microtime(true) - TIMER_START) * 1000, 2),
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
