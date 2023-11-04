<?php

namespace IbIntegrator\Vendors;

use IbIntegrator\HttpClient\GuzzleHttpClient;
use IbIntegrator\Exceptions\ErrorException;

trait Requestor
{

	private static $http_client;
	//
	protected $request;
	protected $response;
	protected $result;

	public function GetClient($args)
	{
		if (!SELF::$http_client) {
			SELF::$http_client = GuzzleHttpClient::getInstance($args);
		}
		return SELF::$http_client;
	}

	public function SetClient($http_client)
	{
		SELF::$http_client = $http_client;
	}

	//

	public function getRequest()
	{
		return $this->request;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getResult()
	{
		return $this->result;
	}

	//

	public function DoRequest(string $method, $request)
	{
		try {
			$guzzle_client = $this->GetClient([
				'base_uri' => parse_url($request['url'], PHP_URL_SCHEME) . '://' . parse_url($request['url'], PHP_URL_HOST),
			]);
			$request = array_merge($request, ['method' => $method]);
			$response = $guzzle_client->GuzzleRequest(
				$method,
				$request['url'] ?? null,
				$request['data'] ?? [],
				$request['headers'] ?? [],
				$request['opt'] ?? [],
			);
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
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		$this->request = $guzzle_client->request;
		$this->response = $guzzle_client->response;
		$this->result = $guzzle_client->result;
		return $result ?? [];
	}

	public function ThrowErrorException(\Throwable $e, $context = null, $message = null, $previous = null)
	{
		$error = ErrorToString($e, $context, $message);
		throw new \Exception($error, $e->getCode(), $previous);
	}

}