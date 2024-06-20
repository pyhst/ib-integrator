<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\PaymentGateway\PaymentGatewayInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class PayOk extends Vendor implements PaymentGatewayInterface
{

	use Requestor;

	public function GenerateSignature($args = [])
	{
		// Not applicable
	}
	public function AuthGetToken($args = [])
	{
		// Not applicable
	}

	public function retrievePrivateKey()
	{
		$private_key = $this->getPrivateKey();
		if (!empty($private_key)) {
			return
				"-----BEGIN RSA PRIVATE KEY-----\n" .
				chunk_split($private_key, 64, "\n") .
				"-----END RSA PRIVATE KEY-----\n";
		}
		if (!$cert_store = file_get_contents($_ENV['PAYOK_PRIVATE_KEY_FILE'])) {
			return "Error: Unable to read the cert file\n";
		}
		return $cert_store;
	}

	//

	public function CreateBilling(Transaction $transaction)
	{
		try {
			$time = date_format(new \DateTime('now', new \DateTimeZone('UTC')), 'Y-m-d\TH:i:s.v\Z');
			$request['url'] = CleanURL(
				$this->getPaymentURL() .
				($transaction->getURL() ?? '/api-pay/payment/V3/order/create-api')
			);
			$request['data'] = [
				'requestTime' => $time,
				'merchantId' => $this->getID(),
				'paymentMethodCode' => $transaction->getPaymentMethod(),
				'countryCode' => 'IDN',
				'merchantOrderId' => $transaction->getReferenceNumber(),
				'amount' => (int) $transaction->getAmount(),
				'currency' => 'RP',
				'notificationUrl' => $this->getCallbackURL(),
				'returnUrl' => $this->getReturnURL(),
				'language' => 'EN',
				'customer' => [
					'name' => $transaction->getCustomerName(),
					'personalId' => $transaction->getCustomerID(),
					'email' => $transaction->getCustomerEmail(),
					'countryName' => 'IDN',
					'city' => $transaction->getCustomerCity(),
					'zip' => '',
					'address' => $transaction->getCustomerAddress(),
					'phone' => $transaction->getCustomerPhone(),
					'deviceId' => $transaction->getCustomerID(),
					'ip' => '',
				],
				'goodsInfo' => [
					'id' => '',
					'name' => $transaction->getDescription(),
					'price' => (int) $transaction->getAmount(),
				],
			];
			$payload = json_encode($request['data']) . "&/api-pay" . "/payment/V3/order/create-api";
			$signature = '';
			$private_key = $this->retrievePrivateKey();
			if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$request['headers'] = [
				'Accept' => 'application/json',
				'sign' => base64_encode($signature),
			];
			$request['option'] = [
				// 'as_json' => true,
			];
			$get = $this->DoRequest('GET', $request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					&& trim(strtoupper($content->code)) == 'SUCCESS'
				) {
					/*
						// Success // VA
							{
								"amount": 15000,
								"code": "SUCCESS",
								"countryCode": "IDN",
								"createTime": "20240609221751",
								"currency": "RP",
								"merchantId": "910061",
								"merchantOrderId": "1717946271",
								"paymentInfo": {
									"content": "3821005989401128",
									"expiredTime": "20240609224151",
									"type": "code"
								},
								"paymentMethodCode": "BNIVA",
								"platformOrderId": "2024060906100000005",
								"status": "PENDING",
								"updateTime": "20240609221751"
							}
						// Success // QRIS
							{
								"amount": 15000,
								"code": "SUCCESS",
								"countryCode": "IDN",
								"createTime": "20240609221723",
								"currency": "RP",
								"merchantId": "910061",
								"merchantOrderId": "1717946242",
								"paymentInfo": {
									"content": "8308293647954790",
									"expiredTime": "20240609224123",
									"type": "code"
								},
								"paymentMethodCode": "QRIS",
								"platformOrderId": "2024060906100000004",
								"status": "PENDING",
								"updateTime": "20240609221723"
							}
					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => (array) $content,
					];
					$status_code = 200;
				} else {
					throw new JsonException(__FUNCTION__, json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content, 400, 902);
			}
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	public function InquiryBilling(Transaction $transaction)
	{
		// Not applicable
	}

	public function CancelBilling(Transaction $transaction)
	{
		// Not applicable
	}

	public function InquiryPayment(Transaction $transaction)
	{
		try {
			$time = date_format(new \DateTime('now', new \DateTimeZone('UTC')), 'Y-m-d\TH:i:s.v\Z');
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				($transaction->getURL() ?? '/api-pay/payment/V3/order/query')
			);
			$request['data'] = [
				'requestTime' => $time,
				'merchantId' => $this->getID(),
				'merchantOrderId' => $transaction->getOrderID(),
			];
			$payload = json_encode($this->request['data']) . "&/api-pay" . "/payment/V3/order/query";
			$signature = '';
			$private_key = $this->retrievePrivateKey();
			if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$request['headers'] = [
				'Accept' => 'application/json',
				'sign' => base64_encode($signature),
			];
			$request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					&& trim(strtoupper($content->code)) == 'SUCCESS'
				) {
					/* // Success inquiry VA
						{
							"amount": 15000,
							"code": "SUCCESS",
							"countryCode": "IDN",
							"createTime": "20240609221751",
							"currency": "Rp",
							"merchantId": "910061",
							"merchantOrderId": "1717946271",
							"paidAmount": 15000,
							"paymentMethodCode": "BNIVA",
							"platformOrderId": "2024060906100000005",
							"status": "SUCCESS",
							"updateTime": "20240609221855"
						}
					*/
					/* // Success inquiry QRIS
						{
							"amount": 15000,
							"code": "SUCCESS",
							"countryCode": "IDN",
							"createTime": "20240609222330",
							"currency": "Rp",
							"merchantId": "910061",
							"merchantOrderId": "1717946609",
							"paymentMethodCode": "QRIS",
							"platformOrderId": "2024060906100000006",
							"status": "PENDING"
						}

					*/
					$res = [
						'status' => '000',
						'request' => $this->request,
						'data' => (array) $content,
					];
				} else {
					throw new JsonException(__FUNCTION__, json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content, 400, 902);
			}
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	public function CancelPayment(Transaction $transaction)
	{
		// Not applicable
	}

	public function RefundPayment(Transaction $transaction)
	{
		// Not applicable
	}

	public function PaymentCallback($request)
	{
		/* // Example incoming data
		*/
		try {
			ValidateArgs((object) $request, [
			]);
			$res = [
				'status' => '000',
				'data' => (array) $request,
			];
			$status_code = 200;
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	//

	public function GetBankList(Transaction $transaction)
	{
		// Not applicable
	}

	public function CheckAccountBalance(Transaction $transaction)
	{
		try {
			$time = time();
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/balance_query'
			);
			$signature = md5(
				'CB' . $time .
				$this->getID() .
				$this->getSecret() .
				'balance_query'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'request_id' => 'CB' . $time,
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success check balance
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Data found",
							"data": {
								"merchant_id": "KMB0001",
								"active_balance": "1079400",
								"pending_balance": "800000"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
					];
				} else {
					throw new JsonException(__FUNCTION__, json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content, 400, 902);
			}
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	public function BankAccountInquiry(Transaction $transaction)
	{
		try {
			$time = time();
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/inquiry'
			);
			$signature = md5(
				'BAI' . $time .
				$this->getID() .
				$this->getSecret() .
				'inquiry'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'project_no' => $this->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => 'BAI' . $time,
				'amount' => $transaction->getAmount(),
				'remit_type' => $transaction->getTransferMethod(),
				'partner_ref_id' => $transaction->getReferenceNumber(),
				'account_no' => $transaction->getCustomerBankAccountNumber(),
				'bank_code' => $transaction->getCustomerBankCode(),
				'transaction_datetime' => $transaction->getTransactionTime(),
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success bank account inquiry
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Data Found",
							"data": {
								"partner_ref_id": "1716270784",
								"inquiry_id": "24052112530718Y6",
								"bank_code": "011",
								"account_name": "7700173383",
								"account_no": "7700173383",
								"account_bank": "Bank Danamon & Danamon Syariah",
								"amount": 10000,
								"admin_fee": 2000,
								"total_amount": 12000,
								"transaction_datetime": "2024-05-21 12:53:04",
								"merchant_id": "KMB0001",
								"project_no": "UJK87AA"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
					];
				} else {
					throw new JsonException(__FUNCTION__, json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content, 400, 902);
			}
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	public function FundTransfer(Transaction $transaction)
	{
		try {
			$time = time();
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/transfer'
			);
			$signature = md5(
				'OTE' . $time .
				$this->getID() .
				$this->getSecret() .
				'transfer'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'project_no' => $this->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => 'OTE' . $time,
				'inquiry_id' => $transaction->getOrderID(),
				'description' => $transaction->getDescription(),
				'transaction_datetime' => $transaction->getTransactionTime(),
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success disb
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Disburse Successful",
							"data": {
								"partner_ref_id": "1716271492",
								"bank_code": "011",
								"account_no": "7700173383",
								"account_bank": "Bank Danamon & Danamon Syariah",
								"amount": 10000,
								"admin_fee": 2000,
								"total_amount": 12000,
								"transaction_datetime": "2024-05-21 13:05:04",
								"merchant_id": "KMB0001",
								"project_no": "UJK87AA",
								"ref_id": "240521130507N64P",
								"status": "success"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
					];
				} else {
					throw new JsonException(__FUNCTION__, json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content, 400, 902);
			}
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	public function CheckFundTransferStatus(Transaction $transaction)
	{
		try {
			$time = time();
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/status_query'
			);
			$signature = md5(
				'CT' . $time .
				$this->getID() .
				$this->getSecret() .
				'status_query'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'request_id' => 'CT' . $time,
				'partner_ref_id' => $transaction->getParam('partner_ref_id'),
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['option'] = [
				// 'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->result)
					&& trim(strtolower($content->result)) == true
				) {
					/* // Success check status
						{
							"result": true,
							"error_code": "000",
							"error_desc": "Data found",
							"data": {
								"transaction_datetime": "2024-05-21 13:05:04",
								"account_no": "7700173383",
								"account_bank": "Bank Danamon & Danamon Syariah",
								"amount": "10000",
								"description": "TEST-TRANSFER",
								"merchant_id": "KMB0001",
								"status": "Success",
								"admin_fee": "2000",
								"partner_ref_id": "1716271492",
								"ref_id": "240521130507N64P"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => $content->data,
					];
				} else {
					throw new JsonException(__FUNCTION__, json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content, 400, 902);
			}
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	public function DisbursementCallback($request)
	{
		// Not applicable
	}

}