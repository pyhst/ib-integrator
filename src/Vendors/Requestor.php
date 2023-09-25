<?php

namespace IbIntegrator\Vendors;

use IbIntegrator\HttpClient\GuzzleHttpClient;
// use IbIntegrator\Vendors\Vendor;
// use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Exceptions\RequestorException;
use IbIntegrator\Exceptions\ApiException;

trait Requestor
{

	// protected $vendor;
	// protected $transaction;
	//
	private static $http_client;
	//
	protected $request;
	protected $response;
	protected $result;

	// public function __construct(Vendor $vendor, Transaction $transaction)
	// {
	// 	$this->vendor = $vendor;
	// 	$this->transaction = $transaction;
	// }

	private static function GetClient($args)
	{
		if (!SELF::$http_client) {
			SELF::$http_client = GuzzleHttpClient::getInstance($args);
		}
		return SELF::$http_client;
	}

	public function DoRequest(string $method, $request)
	{
		// try {
			$client = SELF::GetClient([
				'base_uri' => parse_url($request['url'], PHP_URL_SCHEME) . '://' . parse_url($request['url'], PHP_URL_HOST),
			]);
			$response = $client->GuzzleRequest(
				$method,
				$request['url'] ?? null,
				$request['data'] ?? [],
				$request['headers'] ?? [],
				$request['opt'] ?? [],
			);
print_r($response);
exit();
			if (is_array($response)) {
				$result = [
					'request' => $request,
					'response' => $response,
				];
			} elseif (is_object($response)) {
				$result = [
					'request' => $request,
					'response' => [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					],
				];
				$response->getBody()->rewind();
			} elseif (is_string($response)) {
				$result = $response;
			}
		// } catch (\Throwable $e) {
		// 	$this->ThrowErrorException($e);
			// throw $e;
			// throw new RequestorException($e, __FUNCTION__);
			// $error_message = ErrorString($e, __FUNCTION__);
			// throw new \Exception($error_message, $e->getCode());
			// $error_message = ErrorString($e);
			// throw new \Exception($error_message, $e->getCode());
			// throw new RequestorException($e, __FUNCTION__);
			// $error = ErrorString($e, __FUNCTION__);
			// throw new \Exception($error, $e->getCode());
		// }
		$this->request = $request;
		$this->response = $response;
		return $result ?? [];
	}

	public function ThrowErrorException(\Throwable $e, $context = null)
	{
		$error = ErrorString($e, $context);
		throw new ApiException($error, $e->getCode());
	}

}