<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\PaymentGatewayInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class Duitku extends Vendor implements PaymentGatewayInterface
{

	use Requestor;

	public function GenerateSignature($args = [])
	{

	}
	public function AuthGetToken($args = [])
	{

	}

	//

	public function CreateBilling(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/webapi/api/merchant/v2/inquiry'
			);
			$billing_address = [
				'firstName' => $transaction->getCustomerName(),
				'lastName' => '',
				'address' => $transaction->getCustomerAddress(),
				'city' => $transaction->getCustomerCity(),
				'postalCode' => $transaction->getPostalCode(),
				'phone' => $transaction->getCustomerPhone(),
				'countryCode' => $transaction->getCountryCode(),
			];
			$signature = md5(
				$this->getID() .
				$transaction->getOrderID() .
				(int) $transaction->getAmount() .
				$this->getAPIKey()
			);
			$request['data'] = [
				'merchantCode' => $this->getID(),
				'paymentAmount' => (int) $transaction->getAmount(),
				'paymentMethod' => $transaction->getPaymentMethod(),
				'merchantOrderId' => $transaction->getOrderID(),
				'productDetails' => $transaction->getDescription(),
				'additionalargs' => '', // optional
				'merchantUserInfo' => '', // optional
				'customerVaName' => $transaction->getCustomerName(),
				'email' => $transaction->getCustomerEmail(),
				'phoneNumber' => $transaction->getCustomerPhone(),
				'itemDetails' => $transaction->getItems(),
				'customerDetail' => [
					'firstName' => $transaction->getCustomerName(),
					'lastName' => '',
					'email' => $transaction->getCustomerEmail(),
					'phoneNumber' => $transaction->getCustomerPhone(),
					'billingAddress' => $billing_address,
					'shippingAddress' => $billing_address,
				],
				'callbackUrl' => $this->getCallbackURL(),
				'returnUrl' => $this->getReturnURL(),
				'signature' => $signature,
				'expiryPeriod' => $transaction->getExpireIn(), // In minutes
			];
			$request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($request['data'])),
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
					!empty($content->statusCode)
					&& $content->statusCode == "00"
					&& !empty($content->statusMessage)
					&& trim(strtoupper($content->statusMessage)) == "SUCCESS"
				) {
					/* // Success VA
						{
							"merchantCode": "DS15995",
							"reference": "DS15995232R1EIKIY6HVWPJB",
							"paymentUrl": "https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=BC23QGHL75MGRLOO2MF",
							"vaNumber": "7007014007401309",
							"amount": "100000",
							"statusCode": "00",
							"statusMessage": "SUCCESS"
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

	public function InquiryPayment(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/webapi/api/merchant/transactionStatus'
			);
			$signature = md5(
				$this->getID() .
				$transaction->getOrderID() .
				$this->getAPIKey()
			);
			$request['data'] = [
				'merchantCode' => $this->getID(),
				'merchantOrderId' => $transaction->getOrderID(),
				'signature' => $signature,
			];
			$request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($request['data'])),
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
					!empty($content->statusCode)
					&& $content->statusCode == "00"
					&& !empty($content->statusMessage)
					&& trim(strtoupper($content->statusMessage)) == "SUCCESS"
				) {
					/* // Success
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

	public function CancelPayment(Transaction $transaction)
	{

	}

	public function RefundPayment(Transaction $transaction)
	{

	}

	public function PaymentCallback($request)
	{
		/* // Example incoming data
        {
			"merchantCode": "D6677",
			"amount": "100000",
			"merchantOrderId": "0001285662",
			"productDetail": "Payment for order 0001285662",
			"additionalParam": null,
			"resultCode": "00",
			"signature": "439030a6da086ee13558137f07d4a27d",
			"paymentCode": "VC",
			"merchantUserId": null,
			"reference": "D6677JXVYL752HMAV0AD"
        }
		*/
		try {
			ValidateArgs((object) $request, [
				'merchantOrderId',
				'amount',
				'signature'
			]);
			$signature = md5(
				$this->getID() .
				(int) $request->amount .
				$request->merchantOrderId .
				$this->getSecret()
			);
			if (strcmp($signature, $request->signature) === 0) {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
				$status_code = 200;
			} else {
				throw new JsonException(__FUNCTION__, 'Signature check failed', 400, 901);
			}
		} catch (\Throwable $e) {
			throw new ErrorException($e);
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	//

	public function GetBankList(Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/webapi/api/disbursement/listBank'
			);
			$signature = hash('sha256',
				$this->getParam('DisbursementEmail') .
				$now .
				$this->getSecret()
			);
			$request['data'] = [
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
					&& !empty($content->Banks)
				) {
					/* // Success
						{
							"responseCode": "00",
							"responseDesc": "Success",
							"Banks": [{
								"bankCode": "014",
								"bankName": "PT BANK CENTRAL ASIA TBK.",
								"maxAmountTransfer": "25000000"
							}, {
								"bankCode": "002",
								"bankName": "PT BANK RAKYAT INDONESIA (PERSERO), TBK.",
								"maxAmountTransfer": "25000000"
							}, {
								"bankCode": "200",
								"bankName": "PT BANK TABUNGAN NEGARA (PERSERO)",
								"maxAmountTransfer": "0"
							}, {
								"bankCode": "008",
								"bankName": "PT. BANK MANDIRI (PERSERO), TBK.",
								"maxAmountTransfer": "25000000"
							}]
						}
					*/
					$res = [
						'status' => '000',
						'data' => array_map(function($item) {
							return [
								'name' => strtoupper($item->bankName),
								'code' => $item->bankCode,
								'limit' => (float) $item->maxAmountTransfer,
							];
						}, $content->Banks),
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

	public function CheckAccountBalance(Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/webapi/api/disbursement/checkbalance'
			);
			$signature = hash('sha256',
				$this->getParam('DisbursementEmail') .
				$now .
				$this->getSecret()
			);
			$request['data'] = [
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
							"userId": 26054,
							"email": "dev@np.co.id",
							"balance": 10000000.00,
							"effectiveBalance": 10000000.00,
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

	public function BankAccountInquiry(Transaction $transaction)
	{
		try {
			$now = round(microtime(true) * 1000);
			if (!$transaction->getTransferMethod()) { /*--------------------------------------  // Online transfer method  -------------------------------------------------------*/
				$request['url'] = CleanURL(
					$this->getHostURL() .
					'/webapi/api/disbursement/inquiry'
				);
				$signature = hash('sha256',
					$this->getParam('DisbursementEmail') .
					$now .
					$transaction->getCustomerBankCode() .
					$transaction->getCustomerBankAccountNumber() .
					(int) $transaction->getAmount() .
					$transaction->getPurposeOfTransaction() .
					$this->getSecret()
				);
				$request['data'] = [
					'userId' => $this->getID(),
					'amountTransfer' => (int) $transaction->getAmount(),
					'bankAccount' => $transaction->getCustomerBankAccountNumber(),
					'bankCode' => $transaction->getCustomerBankCode(),
					'email' => $this->getParam('DisbursementEmail'),
					'purpose' => $transaction->getPurposeOfTransaction(),
					'timestamp' => $now,
					'senderId' => $transaction->getSenderID(),
					'senderName' => $transaction->getSenderName(),
					'signature' => $signature,
					'custRefNumber' => $transaction->getOrderID(),
				];
				/* // Success
					{
						"email": "dev@np.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000088986",
						"disburseId": 111273,
						"responseCode": "00",
						"responseDesc": "Success"
					}
				*/
			} else { /*--------------------------------------  // For other transfer method (LLG, RTGS, H2H or BIFAST)  -------------------------------------------------------*/
				$request['url'] = CleanURL(
					$this->getHostURL() .
					'/webapi/api/disbursement/inquiryclearing'
				);
				$signature = hash('sha256',
					$this->getParam('DisbursementEmail') .
					$now .
					$transaction->getCustomerBankCode() .
					$transaction->getTransferMethod() .
					$transaction->getCustomerBankAccountNumber() .
					(int) $transaction->getAmount() .
					$transaction->getPurposeOfTransaction() .
					$this->getSecret()
				);
				$request['data'] = [
					'userId' => $this->getID(),
					'amountTransfer' => (int) $transaction->getAmount(),
					'bankAccount' => $transaction->getCustomerBankAccountNumber(),
					'bankCode' => $transaction->getCustomerBankCode(),
					'email' => $this->getParam('DisbursementEmail'),
					'purpose' => $transaction->getPurposeOfTransaction(),
					'type' => $transaction->getTransferMethod(),
					'timestamp' => $now,
					'senderId' => $transaction->getSenderID(),
					'senderName' => $transaction->getSenderName(),
					'signature' => $signature,
				];
				/* // Failed
					{
						"email": "dev@np.co.id",
						"bankCode": "014",
						"bankAccount": "8760673566",
						"amountTransfer": 10000.0,
						"accountName": "",
						"custRefNumber": "",
						"disburseId": 0,
						"type": "RTGS",
						"responseCode": "-141",
						"responseDesc": "Amount transfer cannot below 100.000.000"
					}
				*/
				/* // Success
					{
						"email": "dev@np.co.id",
						"bankCode": "014",
						"bankAccount": "8760673466",
						"amountTransfer": 150000000.0,
						"accountName": "Test Account",
						"custRefNumber": "000000088998",
						"disburseId": 111285,
						"type": "RTGS",
						"responseCode": "00",
						"responseDesc": "Approved or completed successfully"
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
					// && trim(strtoupper($content->responseDesc)) == "SUCCESS"
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