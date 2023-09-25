<?php

namespace IbIntegrator\Vendors;

class Vendor
{

	/**
	 *
	 * Attributes
	 *
	 */
	protected $id;
	protected $secret;
	protected $token;
	protected $api_key;
	protected $private_key;
	protected $public_key;
	protected $signing_key;
	//
	protected $host_url;
	protected $request_url;
	protected $payment_url;
	protected $callback_url;
	protected $disbursement_callback_url;
	protected $return_url;
	//
	protected $response_url;
	protected $backend_url;
	protected $token_url;
	protected $parser_url;
	//
	protected $merchant_code;
	protected $merchant_token;
	//
	protected $params; // Additional parameters

	public function __construct($client_id = null, $client_secret = null)
	{
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
	}

	//

	public function setID(string $id): void
	{
		$this->id = $id;
	}

	public function getID(): ?string
	{
		return $this->id;
	}

	public function setSecret(string $secret): void
	{
		$this->secret = $secret;
	}

	public function getSecret(): ?string
	{
		return $this->secret;
	}

	public function setToken(string $token): void
	{
		$this->token = $token;
	}

	public function getToken(): ?string
	{
		return $this->token;
	}

	//

	public function setAPIKey(string $api_key): void
	{
		$this->api_key = $api_key;
	}

	public function getAPIKey(): ?string
	{
		return $this->api_key;
	}

	public function setPrivateKey(string $private_key): void
	{
		$this->private_key = $private_key;
	}

	public function getPrivateKey(): ?string
	{
		return $this->private_key;
	}

	public function setPublicKey(string $public_key): void
	{
		$this->public_key = $public_key;
	}

	public function getPublicKey(): ?string
	{
		return $this->public_key;
	}

	public function setSigningKey(string $signing_key): void
	{
		$this->signing_key = $signing_key;
	}

	public function getSigningKey(): ?string
	{
		return $this->signing_key;
	}

	//

	public function setHostURL(string $host_url): void
	{
		$this->host_url = $host_url;
	}

	public function getHostURL(): ?string
	{
		return $this->host_url;
	}

	public function setRequestURL(string $request_url): void
	{
		$this->request_url = $request_url;
	}

	public function getRequestURL(): ?string
	{
		return $this->request_url;
	}

	public function setPaymentURL(string $payment_url): void
	{
		$this->payment_url = $payment_url;
	}

	public function getPaymentURL(): ?string
	{
		return $this->payment_url;
	}

	public function setCallbackURL(string $callback_url): void
	{
		$this->callback_url = $callback_url;
	}

	public function getCallbackURL(): ?string
	{
		return $this->callback_url;
	}

	public function setDisbursementCallbackURL(string $disbursement_callback_url): void
	{
		$this->disbursement_callback_url = $disbursement_callback_url;
	}

	public function getDisbursementCallbackURL(): ?string
	{
		return $this->disbursement_callback_url;
	}

	public function setReturnURL(string $return_url): void
	{
		$this->return_url = $return_url;
	}

	public function getReturnURL(): ?string
	{
		return $this->return_url;
	}

	//

	public function setResponseUrl(string $response_url): void
	{
		$this->response_url = $response_url;
	}

	public function getResponseUrl(): ?string
	{
		return $this->response_url;
	}

	public function setBackendURL(string $backend_url): void
	{
		$this->backend_url = $backend_url;
	}

	public function getBackendURL(): ?string
	{
		return $this->backend_url;
	}

	public function setTokenURL(string $token_url): void
	{
		$this->token_url = $token_url;
	}

	public function getTokenURL(): ?string
	{
		return $this->token_url;
	}

	public function setParserURL(string $parser_url): void
	{
		$this->parser_url = $parser_url;
	}

	public function getParserURL(): ?string
	{
		return $this->parser_url;
	}

	//

	public function setMerchantCode(string $merchant_code): void
	{
		$this->merchant_code = $merchant_code;
	}

	public function getMerchantCode(): ?string
	{
		return $this->merchant_code;
	}

	//

	public function setMerchantToken(string $merchant_token): void
	{
		$this->merchant_token = $merchant_token;
	}

	public function getMerchantToken(): ?string
	{
		return $this->merchant_token;
	}

	//

	public function setParams(array $params): void
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

}