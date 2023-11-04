<?php

namespace IbIntegrator\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class GuzzleHttpClient
{

	private static $guzzle_instance;
	protected $guzzle_client;
	//
	protected $args;
	protected $timeout = 40;
	protected $effective_uri;
	//
	public $request;
	public $response;
	public $result;

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
			// Overide
			if (!empty($opt['as_json']) && $opt['as_json']) {
				$type = 'json';
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
			$this->request = [
				'url' => $url,
				'method' => $method,
				'headers' => $headers,
				'data' => $data,
				'type' => $type,
				'opt' => $opt,
			];
			$this->response = $response;
			//
			if (!empty($opt['to_json']) && $opt['to_json']) {
				$result = [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					];
				$response->getBody()->rewind();
			} elseif (isset($opt['to_uri']) && !empty($opt['to_uri'])) {
				$result = (string) strval($this->effective_uri);
			} else {
				$result = $response; // Default is return as PSR7 response
			}
		} catch (RequestException|ClientException $e) {
			SELF::ExceptionHandler($e);
		} catch (\Throwable $e) {
			SELF::ExceptionHandler($e);
		}
		$this->result = $result;
		return $result ?? [];
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
		$uri = $request->getUri();
		$userInfo = $uri->getUserInfo();
		if (false !== ($pos = \strpos($userInfo, ':'))) {
			$uri = $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
		}
		$message = sprintf(
			'%s/%s %s [%s:%s] "%s" ',
			$label,
			$response->getReasonPhrase(),
			$response->getStatusCode(),
			$request->getMethod(),
			$uri,
			$response->getBody()->getContents()
		);
		//
		$status_code = (int) $e->getResponse()->getStatusCode();
		$error_code = $e->getCode();
		throw new \IbIntegrator\Exceptions\RequestorException($message, $status_code, $error_code);
	}

}