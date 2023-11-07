<?php

namespace IbIntegrator\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class GuzzleHttpClient
{

	private static $guzzle_instance;
	protected $guzzle_client;
	//
	protected $args;
	protected $timeout = 40;
	//
	public $effective_uri;
	public $request;
	public $response;
	// public $result;
	public $stats;

	//

	public function __construct($args)
	{
		$this->args = array_merge([
			'verify' => false,
			'timeout' => $this->timeout,
			// 'read_timeout' => 10,
		], $args);
		if (!$this->guzzle_client) {
			$this->guzzle_client = new Client($this->args);
		}
	}

	public static function getInstance($args = [])
	{
		if (!SELF::$guzzle_instance) {
			SELF::$guzzle_instance = new SELF($args);
		}
		return SELF::$guzzle_instance;
	}

	//

	public function GuzzleRequest(string $method, string $url, $data, array $headers = [], array $options = [])
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
			// Overide
			if (!empty($options['as_json']) && $options['as_json']) {
				$type = 'json';
			}
			// Request options
			$options = array_merge($options, [
				'headers' => $headers,
				$type => $data,
				'on_stats' => function(TransferStats $stats) {
						$this->effective_uri = $stats->getEffectiveUri();
						$this->stats = $stats;
					},
				'timeout' => $options['timeout'] ?? $this->timeout,
			]);
			// Go
			/*--------------------------------------  // Changed from  -------------------------------------------------------*/
			// $response = $this->guzzle_client->request($method, $url, $options);
			// $this->request = [
			// 	'url' => $url,
			// 	'method' => $method,
			// 	'headers' => $headers,
			// 	'data' => $data,
			// 	'type' => $type,
			// 	'opt' => $opt,
			// ];
			// $this->response = $response;
			/*--------------------------------------  // Changed into: Return of PSR objects  -------------------------------------------------------*/
			switch ($type) {
				case 'query':
					$request = new Request($method, $url . '?' . http_build_query($data), $headers);
					break;
				case 'form_params':
					$request = new Request($method, $url, $headers, http_build_query($data));
					break;
				case 'json':
					$request = new Request($method, $url, $headers, json_encode($data));
					break;
			}
			$response = $this->guzzle_client->send($request, $options);
			/*--------------------------------------  // Changed ends  -------------------------------------------------------*/
			/*--------------------------------------  // Disable starts  -------------------------------------------------------*/
			//
			// if (!empty($opt['to_json']) && $opt['to_json']) {
			// 	$result = [
			// 			'content' => $response->getBody()->getContents(),
			// 			'status_code' => (int) $response->getStatusCode(),
			// 			'headers' => $response->getHeaders(),
			// 		];
			// 	$response->getBody()->rewind();
			// } elseif (isset($opt['to_uri']) && !empty($opt['to_uri'])) {
			// 	$result = (string) strval($this->effective_uri);
			// } else {
			// 	$result = $response; // Default is return as PSR7 response
			// }
			/*--------------------------------------  // Disable ends  -------------------------------------------------------*/
		} catch (RequestException|ClientException $e) {
			$this->request = $e->getRequest();
			$this->response = $e->getResponse();
			SELF::ExceptionHandler($e);
		} catch (\Throwable $e) {
			$this->request = $e->getRequest();
			$this->response = $e->getResponse();
			throw $e;
		}
		$this->request = $request;
		$this->response = $response;
		// $this->result = $result;
		// return $result ?? [];
		return $response;
	}

	private static function ExceptionHandler($e)
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
		//
		$uri = $request->getUri();
		$data = $request->getBody()->__toString();
		$method = $request->getMethod();
		$headers = [];
		foreach ($request->getHeaders() as $name => $values) {
			$headers[] = $name . ": " . implode(", ", $values);
		}
		if (is_string($data) && is_array(json_decode($data, true))) {
			$data = json_decode($data, true);
		}
		$result = $response->getBody()->getContents();
		if (is_string($result) && is_array(json_decode($result, true))) {
			$result = json_decode($result, true);
		}
		$req = [
			'url' => $uri,
			'method' => $method,
			'headers' => $headers,
			'data' => $data,
		];
		$res = [
			'status_code' => $response->getStatusCode(),
			'content' => $result,
		];
		$request_response = json_encode([$req, $res]);
		//
		$userInfo = $uri->getUserInfo();
		if (false !== ($pos = \strpos($userInfo, ':'))) {
			$uri = $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
		}
		$message = sprintf(
			'%s/%s %s [%s:%s] "%s" --- ',
			$label,
			$response->getReasonPhrase(),
			$response->getStatusCode(),
			$request->getMethod(),
			$uri,
			$request_response,
		);
		//
		$status_code = (int) $e->getResponse()->getStatusCode();
		$error_code = $e->getCode();
		throw new \IbIntegrator\Exceptions\RequestorException($message, $status_code, $error_code);
	}

}