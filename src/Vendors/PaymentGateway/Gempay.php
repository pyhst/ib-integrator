<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\PaymentGateway\PaymentGatewayInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class Gempay extends Vendor implements PaymentGatewayInterface
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

	//

	public function CreateBilling(Transaction $transaction)
	{
		try {
			$signature = md5(
				$transaction->getReferenceNumber() .
				(int) $transaction->getAmount() .
				$this->getID() .
				$transaction->getPaymentChannel() .
				$this->getSecret() .
				$this->getParam('GEMPAY_PROJECT_NO')
			);
			$request['url'] = CleanURL(
				$this->getPaymentURL() .
				'/v1/direct'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'project_no' => $this->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => $transaction->getReferenceNumber(),
				'amount' => (int) $transaction->getAmount(),
				'signature' => $signature,
				'channel' => $transaction->getPaymentChannel(),
				'description' => $transaction->getDescription(),
				'callback_url' => $this->getCallbackURL(),
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['options'] = [
				// 'as_json' => true,
			];
			$get = $this->DoRequest('GET', $request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status)
					&& trim(strtolower($content->status)) == true
				) {
					/*
						// Success // VA
							{
								"status": true,
								"error_code": "P00",
								"error_desc": "Data format is good",
								"ref_id": "20240423154336-662774B805BC7",
								"request_id": "1713861815",
								"channel": "cimb_va",
								"amount": "15000",
								"admin_fee": "4900",
								"total_amount": "19900",
								"virtual_account": "9999900000000003",
								"qrcode": null
							}
						// Success // QRIS
							{
								"status": true,
								"error_code": "P00",
								"error_desc": "Data format is good",
								"ref_id": "20240423162310-66277DFE23CDA",
								"request_id": "1713864189",
								"channel": "MBayar_QR",
								"amount": "15000",
								"admin_fee": "117",
								"total_amount": "15117",
								"virtual_account": null,
								"qrcode": "https:\/\/sandbox-api.gempay.online\/v1\/qrcode?UzRNUDFsRWRUTldXTVhjbmVZdU1qWSs0blhrYyswSzRKMU5HK1p6ZU9aMD0="
							}
					*/
					$res = [
						'status' => '000',
						'request' => $request,
						'data' => (array) $content,
					];
					$status_code = 200;
				} else {
					throw new JsonException(__FUNCTION__, 'Unknown status: ' . json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content ?? 'Unknown error', 400, 902);
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
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/v1/history'
			);
			$signature = md5(
				$this->getID() .
				$this->getSecret() .
				$this->getParam('GEMPAY_PROJECT_NO')
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'project_no' => $this->getParam('GEMPAY_PROJECT_NO'),
				'start' => $transaction->getParam('start'),
				'length' => $transaction->getParam('length'),
				'ref_id' => $transaction->getParam('ref_id'),
				'order' => $transaction->getParam('order'),
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['options'] = [
				// 'as_json' => true,
			];
			$get = $this->DoRequest('GET', $request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status)
					&& trim(strtolower($content->status)) == true
				) {
					/* // Success inquiry QRIS
						{
							"status": true,
							"error_code": "00",
							"error_desc": "Data found",
							"total_rows": 1,
							"data": [
								{
										"transaction_id": "9052",
										"channel": "Qris M-Bayar",
										"amount": "15000",
										"status": "Success",
										"ref_id": "20240423162310-66277DFE23CDA",
										"order_datetime": "2024-04-23 16:23:10",
										"payment_datetime": "2024-04-23 16:24:11",
										"request_id": "1713864189",
										"merchant_id": "KMB0001",
										"project_no": "UJK87AA",
										"admin_fee": "117",
										"total_amount": "15117"
								}
							]
						}
					*/
					$res = [
						'status' => '000',
						'request' => $request,
						'data' => (array) $content->data,
					];
				} else {
					throw new JsonException(__FUNCTION__, 'Unknown status: ' . json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content ?? 'Unknown error', 400, 902);
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
			$request_id = $transaction->getRequestID() ?? 'CB' . $time;
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/balance_query'
			);
			$signature = md5(
				$request_id .
				$this->getID() .
				$this->getSecret() .
				'balance_query'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'request_id' => $request_id,
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['options'] = [
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
						'request' => $request,
						'data' => (array) $content,
					];
				} else {
					throw new JsonException(__FUNCTION__, 'Unknown status: ' . json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content ?? 'Unknown error', 400, 902);
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
			$request_id = $transaction->getRequestID() ?? 'BAI' . $time;
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/inquiry'
			);
			$signature = md5(
				$request_id .
				$this->getID() .
				$this->getSecret() .
				'inquiry'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'project_no' => $this->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => $request_id,
				'amount' => $transaction->getAmount(),
				'remit_type' => $transaction->getTransferMethod(),
				'partner_ref_id' => $transaction->getReferenceNumber(),
				'account_no' => $transaction->getCustomerBankAccountNumber(),
				'bank_code' => $transaction->getCustomerBankCode(),
				'transaction_datetime' => $transaction->getTransactionDate(),
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['options'] = [
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
						'request' => $request,
						'data' => (array) $content,
					];
				} else {
					throw new JsonException(__FUNCTION__, 'Unknown status: ' . json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content ?? 'Unknown error', 400, 902);
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
			$request_id = $transaction->getRequestID() ?? 'TF' . $time;
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/transfer'
			);
			$signature = md5(
				$request_id .
				$this->getID() .
				$this->getSecret() .
				'transfer'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'project_no' => $this->getParam('GEMPAY_PROJECT_NO'),
				'request_id' => $request_id,
				'inquiry_id' => $transaction->getOrderID(),
				'description' => $transaction->getDescription(),
				'transaction_datetime' => $transaction->getTransactionDate(),
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['options'] = [
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
						'request' => $request,
						'data' => (array) $content,
					];
				} else {
					throw new JsonException(__FUNCTION__, 'Unknown status: ' . json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content ?? 'Unknown error', 400, 902);
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
			$request_id = $transaction->getRequestID() ?? 'CTF' . $time;
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/api/status_query'
			);
			$signature = md5(
				$request_id .
				$this->getID() .
				$this->getSecret() .
				'status_query'
			);
			$request['data'] = [
				'merchant_id' => $this->getID(),
				'request_id' => $request_id,
				'partner_ref_id' => $transaction->getParam('partner_ref_id'),
				'signature' => $signature,
			];
			$request['headers'] = [
				'Accept' => 'application/json',
			];
			$request['options'] = [
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
						'request' => $request,
						'data' => (array) $content,
					];
				} else {
					throw new JsonException(__FUNCTION__, 'Unknown status: ' . json_encode($content), 400, 901);
				}
			} else {
				throw new JsonException(__FUNCTION__, $content ?? 'Unknown error', 400, 902);
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