<?php

namespace IbIntegrator\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

use IbIntegrator\Exceptions\ApiException;

class GuzzleHttpClient
{

	private static $guzzle_instance;
	protected $guzzle_client;
	//
	protected $args;
	protected $timeout = 40;
	protected $effective_uri;
	//
	protected $request;
	protected $response;
	protected $result;

	//

	public static function getInstance($args = [])
	{
		if (!SELF::$guzzle_instance) {
			SELF::$guzzle_instance = new SELF($args);
		}
		return SELF::$guzzle_instance;
	}

	public function __construct($args)
	{
		$this->args = array_merge([
			'verify' => false,
			'timeout' => $this->timeout,
			// 'read_timeout' => 10,
		], $args);
		if (!$this->guzzle_client) {
			$this->guzzle_client = new GuzzleClient($this->args);
		}
	}

	private static function HandleAPIError($e)
	{
		$request = $e->getRequest();
		$response = $e->getResponse();
		$level = (int) \floor($response->getStatusCode() / 100);
		if ($level === 4) {
			$label = 'Client error';
		} elseif ($level === 5) {
			$label = 'Server error';
		} else {
			$label = 'Unsuccessful request';
		}
		$uri = $request->getUri();
		$userInfo = $uri->getUserInfo();
		if (false !== ($pos = \strpos($userInfo, ':'))) {
			$uri = $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
		}
		$message = sprintf(
			$label,
			'%s %s [%s:%s] "%s" ',
			$response->getReasonPhrase(),
			$response->getStatusCode(),
			$request->getMethod(),
			$uri,
			$response->getBody()->getContents()
		);
		//
		$status_code = (int) $e->getResponse()->getStatusCode();
		$error_code = $e->getCode();
		// throw new ApiException($message, $status_code, $error_code);
		throw new \Exception($message, $status_code);
		// return [
		// 	'content' => [
		// 		'status' => 999,
		// 		'message' => $message,
		// 		'data' => null,
		// 	],
		// 	'status_code' => $status_code,
		// 	'error_code' => $error_code,
		// ];
	}

	//

	public function GuzzleRequest(string $method, string $url, $data, array $headers = [], array $opt = [])
	{
		try {
			$method = trim(strtoupper($method));
			switch ($method) {
				case 'GET':
					$type = 'query';
					break;
				case 'POST':
					$type = 'form_params';
					break;
				case 'JSON':
					$type = 'json';
					break;
			}
			// Request options
			$options = array_merge($opt, [
				'headers' => $headers,
				$type => $data,
				'on_stats' => function(TransferStats $stats) {
						$this->effective_uri = $stats->getEffectiveUri();
					},
				'timeout' => $opt['timeout'] ?? $this->timeout,
			]);
			// Go
			$response = $this->guzzle_client->request($method, $url, $options);
			//
			if (!empty($opt['to_json']) && $opt['to_json']) {
				$result = [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					];
				// $response->getBody()->rewind();
				// $this->request = $request;
				// $this->response = $response;
			} elseif (isset($opt['to_uri']) && !empty($opt['to_uri'])) {
				$result = (string) strval($this->effective_uri);
			} else {
				// Default is return as PSR7 response
				$result = $response;
			}
		} catch (\Throwable $e) {
			// throw new \Exception($e->getMessage(), $e->getCode());
			SELF::HandleAPIError($e);
		} catch (ClientException $e) {
			// throw new \Exception($e->getMessage(), $e->getCode());
			SELF::HandleAPIError($e);
		} catch (RequestException $e) {
			// throw new \Exception($e->getMessage(), $e->getCode());
			SELF::HandleAPIError($e);
		// 	throw $e;
			// throw new ExceptionsRequestException($e, __FUNCTION__);
			// throw new ApiException($message, $status_code, $error_code);
			// if ($e->hasResponse()) {
			// 	SELF::HandleAPIError($e);
			// // 	// $result = SELF::_HandleAPIError($e);
			// }
		}
		return $result ?? [];
	}

}