<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\PaymentGateway\PaymentGatewayInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class AyoConnect extends Vendor implements PaymentGatewayInterface
{

	use Requestor;

	public function GenerateSignature($args = [])
	{
		// Not applicable
	}

	public function AuthGetToken($args = [])
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/v1/oauth/client_credential/accesstoken?grant_type=client_credentials'
			);
			$request['data'] = [
				'client_id' => $this->getID(),
				'client_secret' => $this->getSecret(),
			];
			$request['headers'] = [[
				'Content-Type' => 'application/x-www-form-urlencoded',
			]];
			$request['opt'] = [
				'to_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->accessToken)
				) {
					/* // Success token
						{
							"apiProductList": "[of-oauth, bank-account-disbursement-sandbox]",
							"organizationName": "ayoconnect-open-finance",
							"developer.email": "xn@ns.id",
							"tokenType": "BearerToken",
							"responseTime": "20231013034555",
							"clientId": "tBPrfKAluJkesLVvevUCyagPgTmtKA20HX9uleuLof4u1A8b",
							"accessToken": "0hVfHjXjmiqNLWAS3bWNlaXQ5OnO",
							"expiresIn": "3599"
						}
					*/
					$res = [
						'status' => '000',
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

	//

	public function CreateBilling(Transaction $transaction)
	{
		// Not applicable
	}

	public function InquiryPayment(Transaction $transaction)
	{
		// Not applicable
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
		// Not applicable
	}

	//

	public function GetBankList(Transaction $transaction)
	{
		// Not applicable
	}

	public function CheckAccountBalance(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/api/v1/merchants/balance'
			);
			$request['data'] = [
				'transactionId' => $transaction->getOrderID(),
			];
			$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$rand = substr(str_shuffle($permitted_chars), 0, 32);
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->getToken(),
				'A-Correlation-ID' => $rand,
				'A-Merchant-Code' => $this->getMerchantCode(),
			];
			$request['opt'] = [
				'as_json' => true,
				'timeout' => 60,
				'to_json' => true,
			];
			$get = $this->DoRequest('GET', $request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					// && $content->code == "200"
					&& !empty($content->message)
					&& trim(strtoupper($content->message)) == "OK"
				) {
					/* // Success
						{
							"code": 200,
							"message": "ok",
							"responseTime": "20231013110351",
							"transactionId": "16971698312CG6FXB3RSWTIKYUHQNE8Z",
							"referenceNumber": "76b10a791d5145589431f47f48831578",
							"merchantCode": "NSIPAY",
							"accountInfo": [
								{
										"availableBalance": {
											"value": "0.00",
											"currency": "IDR"
										}
								}
							]
						}
					*/
					$res = [
						'status' => '000',
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

	public function BankAccountInquiry(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/api/v1/bank-disbursements/beneficiary'
			);
			$request['data'] = [
				'transactionId' => $transaction->getOrderID(),
				'phoneNumber' => $transaction->getCustomerPhone(),
				'customerDetails' => [
					'ipAddress' => $transaction->getIPAddress(),
				],
				'beneficiaryAccountDetails' => [
					'accountNumber' => $transaction->getCustomerBankAccountNumber(),
					'bankCode' => $transaction->getCustomerBankCode(),
				]
			];
			$permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$rand = substr(str_shuffle($permitted_chars), 0, 32);
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->getToken(),
				'A-Correlation-ID' => $rand,
				'A-Merchant-Code' => $this->getMerchantCode(),
				'A-Latitude' => '-6.200000',
				'A-Longitude' => '-106.816666',
			];
			$request['opt'] = [
				'as_json' => true,
				'timeout' => 60,
				'to_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					// && $content->code == "202"
					&& !empty($content->message)
					&& trim(strtoupper($content->message)) == "OK"
				) {
					/* // Success
						{
							"code": 202,
							"message": "ok",
							"responseTime": "20231018071425",
							"transactionId": "16976132629RIF0WT1X23MKVGPJZQ6CB",
							"referenceNumber": "2963c24b207947ac8e4030d94fb89464",
							"customerId": "NSIPAY-11B3LLUQ",
							"beneficiaryDetails": {
								"beneficiaryAccountNumber": "510654300",
								"beneficiaryBankCode": "GNESIDJA",
								"beneficiaryBankName": "PT. BANK GANESHA",
								"beneficiaryId": "BE_1c976c0458",
								"beneficiaryName": "N/A",
								"accountType": "N/A"
							}
						}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
						'request' => (array) $request,
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

	public function FundTransfer(Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			if (!$transaction->getTransferMethod()) { /*--------------------------------------  // Online transfer method  -------------------------------------------------------*/
				$request['url'] = CleanURL(
					$this->getHostURL() .
					'/webapi/api/disbursement/transfer'
				);
				$signature = hash('sha256',
					$this->getParam('DisbursementEmail') .
					$now .
					$transaction->getCustomerBankCode() .
					$transaction->getCustomerBankAccountNumber() .
					$transaction->getCustomerBankAccountName() .
					$transaction->getOrderID() .
					(int) $transaction->getAmount() .
					$transaction->getPurposeOfTransaction() .
					$transaction->getDisbursementID() .
					$this->getSecret()
				);
				$request['data'] = [
					'disburseId' => $transaction->getDisbursementID(),
					'userId' => $this->getID(),
					'email' => $this->getParam('DisbursementEmail'),
					'bankCode' => $transaction->getCustomerBankCode(),
					'bankAccount' => $transaction->getCustomerBankAccountNumber(),
					'amountTransfer' => (int) $transaction->getAmount(),
					'accountName' => $transaction->getCustomerBankAccountName(),
					'custRefNumber' => $transaction->getOrderID(),
					'purpose' => $transaction->getPurposeOfTransaction(),
					'timestamp' => $now,
					'senderName' => $transaction->getSenderName(),
					'signature' => $signature,
				];
				/* // Success
					{
						"email": "dev@np.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000089001",
						"responseCode": "00",
						"responseDesc": "Success"
					}
				*/
			} else { /*--------------------------------------  // For other transfer method (LLG, RTGS, H2H or BIFAST)  -------------------------------------------------------*/
				$request['url'] = CleanURL(
					$this->getHostURL() .
					'/webapi/api/disbursement/transferclearing'
				);
				$signature = hash('sha256',
					$this->getParam('DisbursementEmail') .
					$now .
					$transaction->getCustomerBankCode() .
					$transaction->getTransferMethod() .
					$transaction->getCustomerBankAccountNumber() .
					$transaction->getCustomerBankAccountName() .
					$transaction->getOrderID() .
					(int) $transaction->getAmount() .
					$transaction->getPurposeOfTransaction() .
					$transaction->getDisbursementID() .
					$this->getSecret()
				);
				$request['data'] = [
					'disburseId' => $transaction->getDisbursementID(),
					'userId' => $this->getID(),
					'email' => $this->getParam('DisbursementEmail'),
					'bankCode' => $transaction->getCustomerBankCode(),
					'bankAccount' => $transaction->getCustomerBankAccountNumber(),
					'amountTransfer' => (int) $transaction->getAmount(),
					'accountName' => $transaction->getCustomerBankAccountName(),
					'custRefNumber' => $transaction->getOrderID(),
					'purpose' => $transaction->getPurposeOfTransaction(),
					'type' => $transaction->getTransferMethod(),
					'timestamp' => $now,
					'signature' => $signature,
				];
				/* // Success
					{
						"email": "dev@np.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000089001",
						"responseCode": "00",
						"responseDesc": "Success"
					}
				*/
			}
			$request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($request['data'])),
			]];
			$request['opt'] = [
				'to_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& $content->responseCode == "00"
					&& !empty($content->responseDesc)
					&& trim(strtoupper($content->responseDesc)) == "SUCCESS"
				) {
					$res = [
						'status' => '000',
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

	public function CheckFundTransferStatus(Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/webapi/api/disbursement/inquirystatus'
			);
			$signature = hash('sha256',
				$this->getParam('DisbursementEmail') .
				$now .
				$transaction->getDisbursementID() .
				$this->getSecret()
			);
			$request['data'] = [
				'disburseId' => $transaction->getDisbursementID(),
				'userId' => $this->getID(),
				'email' => $this->getParam('DisbursementEmail'),
				'timestamp' => $now,
				'signature' => $signature,
			];
			$request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($request['data'])),
			]];
			$request['opt'] = [
				'to_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& $content->responseCode == "00"
					&& !empty($content->responseDesc)
					&& trim(strtoupper($content->responseDesc)) == "SUCCESS"
				) {
					/* // Success
						{
							"bankCode": "014",
							"bankAccount": "8760673566",
							"amountTransfer": 10000.00,
							"accountName": "Test Account",
							"custRefNumber": "000000089056",
							"responseCode": "00",
							"responseDesc": "Success"
						}
					*/
					$res = [
						'status' => '000',
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

	public function DisbursementCallback($request)
	{

	}

}