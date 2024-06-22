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

	public function RetrievePrivateKey()
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
			$time = time();
			$timestamp = date("Y-m-d", $time) . "T" . date("H:i:s.B", $time) . "+07:00";
			$payment_method = $transaction->getPaymentMethod();
			$payment_channel = $transaction->getPaymentChannel();
			switch ($payment_method) {
				case 'QRIS':
					$url = '/payment/v2.1/qris/create';
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						$url
					);
					$request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->getID(),
						// 'storeId' => 'STORE01',
						'paymentType' => 'QRIS',
						'amount' => (int) $transaction->getAmount() . '.00',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'notifyUrl' => $this->getCallbackURL(),
						'productName' => $transaction->getDescription(),
						'expire' => (int) $transaction->getExpireIn(), // minutes
						'productInfo' => [[
							'id' => $transaction->getProductID(),
							'name' => $transaction->getProductName(),
							'price' => (int) $transaction->getProductPrice() . '.00',
							'type' => $transaction->getProductType(),
							'url' => $transaction->getProductURL(),
							'quantity' => $transaction->getProductQuantity(),
						]],
					];
					break;
				case 'EWALLET':
					$url = '/payment/v2.1/ewallet/create';
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						$url
					);
					$request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->getID(),
						// 'storeId' => 'STORE01',
						'paymentType' => $payment_channel,
						'amount' => (int) $transaction->getAmount() . '.00',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'notifyUrl' => $this->getCallbackURL(),
						'paymentParams' => [
							'redirectUrl' => $this->getReturnURL(),
							'phoneNumber' => $transaction->getCustomerPhone(),
						],
						'productName' => $transaction->getDescription(),
						'productInfo' => [[
							'id' => $transaction->getProductID(),
							'name' => $transaction->getProductName(),
							'price' => (int) $transaction->getProductPrice() . '.00',
							'type' => $transaction->getProductType(),
							'url' => $transaction->getProductURL(),
							'quantity' => $transaction->getProductQuantity(),
						]],
					];
					break;
				case 'VA':
					$url = '/payment/v2.1/va/create';
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						$url
					);
					$request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->getID(),
						// 'storeId' => 'STORE01',
						'paymentType' => $payment_channel,
						'amount' => (int) $transaction->getAmount() . '.00',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'notifyUrl' => $this->getCallbackURL(),
						'payer' => $transaction->getCustomerName(),
						'productName' => $transaction->getDescription(),
						'productInfo' => [[
							'id' => $transaction->getProductID(),
							'name' => $transaction->getProductName(),
							'price' => (int) $transaction->getProductPrice() . '.00',
							'type' => $transaction->getProductType(),
							'url' => $transaction->getProductURL(),
							'quantity' => $transaction->getProductQuantity(),
						]],
					];
					break;
				case 'CSTORE':
					break;
				case 'CC':
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$hash = strtolower(hash("sha256", json_encode($request['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$hash,
				$timestamp
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->getID(),
				'X-REQUEST-ID' => $transaction->getRequestID() ?? date('YmdHis', $time),
			];
			//
			$request['options'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					isset($content->errCode)
					&& $content->errCode == '0'
				) {
					/* // Success QRIS
						{
							"merchantId": "010430",
							"requestId": "20240621154626",
							"errCode": "0",
							"paymentType": "QRIS",
							"amount": "15000.00",
							"merchantTradeNo": "1718959586",
							"createTime": "20240621154629",
							"platformTradeNo": "2024062143000000029",
							"expiredTime": "20240622154629",
							"status": "01",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959586",
										"name": "BILLING1",
										"price": 15000,
										"type": "QRIS",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"qrCode": "MOCK-QRIS:2024062143000000029",
							"qrisUrl": "https://sit-payer.paylabs.co.id/payer-api/qr?340c5b202e6a48b0a5bed402a21d699aMOCK-QRIS%3A2024062143000000029"
						}
					*/
					/* // Success EWALLET
						{
							"merchantId": "010430",
							"requestId": "20240621155035",
							"errCode": "0",
							"paymentType": "DANABALANCE",
							"amount": "15000.00",
							"merchantTradeNo": "1718959835",
							"createTime": "20240621155038",
							"platformTradeNo": "2024062143000000033",
							"expiredTime": "20240621155102",
							"status": "01",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959835",
										"name": "BILLING1",
										"price": 15000,
										"type": "EWALLET",
										"quantity": 1
								}
							],
							"redirectUrl": "http://tester.com/secure/callback/demo",
							"transFeeRate": "0.001000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10015.00",
							"paymentActions": {
								"mobilePayUrl": "https://sit-payer.paylabs.co.id/payer-api/v1/callback/forward/MDA5LTAwOQ==/8f3edf2d126d97b0dc793331297246d06eecfedf13d8e24cd2f6165a13cf61ce"
							}
						}
					*/
					/* // Success VA
						{
							"merchantId": "010430",
							"requestId": "20240621155814",
							"errCode": "0",
							"paymentType": "MuamalatVA",
							"amount": "15000.00",
							"merchantTradeNo": "1718960294",
							"createTime": "20240621155817",
							"platformTradeNo": "2024062143000000034",
							"expiredTime": "20240622155817",
							"status": "01",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718960294",
										"name": "BILLING1",
										"price": 15000,
										"type": "VA",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"vaCode": "9999900000000001"
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
			$time = time();
			$timestamp = date("Y-m-d", $time) . "T" . date("H:i:s.B", $time) . "+07:00";
			switch ($transaction->getPaymentMethod()) {
				case 'QRIS':
					$url = '/payment/v2.1/qris/query';
					$request['url'] = CleanURL(
						$this->getRequestURL() .
						$url
					);
					$request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->getID(),
						// 'storeId' => 'STORE01',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'paymentType' => $transaction->getPaymentMethod(),
					];
					break;
				case 'EWALLET':
					$url = '/payment/v2.1/ewallet/query';
					$request['url'] = CleanURL(
						$this->getRequestURL() .
						$url
					);
					$request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->getID(),
						// 'storeId' => 'STORE01',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'paymentType' => $transaction->getPaymentChannel(),
					];
					break;
				case 'VA':
					$url = '/payment/v2.1/va/query';
					$request['url'] = CleanURL(
						$this->getRequestURL() .
						$url
					);
					$request['data'] = [
						'requestId' => $transaction->getRequestID() ?? date('YmdHis', $time),
						'merchantId' => $this->getID(),
						// 'storeId' => 'STORE01',
						'merchantTradeNo' => $transaction->getReferenceNumber(),
						'paymentType' => $transaction->getPaymentChannel(),
					];
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$hash = strtolower(hash("sha256", json_encode($request['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
			$string_to_sign = implode(':', [
				'POST',
				$url,
				$hash,
				$timestamp
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($string_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			$request['headers'] = [
				'Content-Type' => 'application/json;charset=utf-8',
				'X-TIMESTAMP' => $timestamp,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->getID(),
				'X-REQUEST-ID' => $transaction->getRequestID() ?? date('YmdHis', $time),
			];
			//
			$request['options'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					isset($content->errCode)
					&& $content->errCode == '0'
				) {
					/* // Success inquiry QRIS
						{
							"merchantId": "010430",
							"requestId": "20240621160531",
							"errCode": "0",
							"paymentType": "QRIS",
							"amount": "15000.00",
							"merchantTradeNo": "1718959724",
							"createTime": "20240621154847",
							"platformTradeNo": "2024062143000000032",
							"successTime": "20240621154948",
							"expiredTime": "20240622154847",
							"status": "02",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959724",
										"name": "BILLING1",
										"price": 15000,
										"type": "QRIS",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"qrCode": "MOCK-QRIS:2024062143000000032",
							"qrisUrl": "https://sit-payer.paylabs.co.id/payer-api/qr?8736791888f5d6107abea94c42853b0aMOCK-QRIS%3A2024062143000000032"
						}
					*/
					/* // Success inquiry EWALLET
						{
							"merchantId": "010430",
							"requestId": "20240621160842",
							"errCode": "0",
							"paymentType": "DANABALANCE",
							"amount": "15000.00",
							"merchantTradeNo": "1718959835",
							"createTime": "20240621155038",
							"platformTradeNo": "2024062143000000033",
							"successTime": "20240621155140",
							"expiredTime": "20240621155102",
							"status": "02",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718959835",
										"name": "BILLING1",
										"price": 15000,
										"type": "EWALLET",
										"quantity": 1
								}
							],
							"redirectUrl": "http://sb.tf2us.com/secure/callback/demo",
							"transFeeRate": "0.001000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10015.00",
							"paymentActions": {
								"mobilePayUrl": "https://sit-payer.paylabs.co.id/payer-api/v1/callback/forward/MDA5LTAwOQ==/8f3edf2d126d97b0dc793331297246d06eecfedf13d8e24cd2f6165a13cf61ce"
							}
						}
					*/
					/* // Success inquiry VA
						{
							"merchantId": "010430",
							"requestId": "20240621161023",
							"errCode": "0",
							"paymentType": "MuamalatVA",
							"amount": "15000.00",
							"merchantTradeNo": "1718960294",
							"createTime": "20240621155817",
							"platformTradeNo": "2024062143000000034",
							"successTime": "20240621155919",
							"expiredTime": "20240622155817",
							"status": "02",
							"productName": "BILLING1",
							"productInfo": [
								{
										"id": "1718960294",
										"name": "BILLING1",
										"price": 15000,
										"type": "VA",
										"quantity": 1
								}
							],
							"transFeeRate": "0.000000",
							"transFeeAmount": "10000.00",
							"totalTransFee": "10000.00",
							"vaCode": "9999900000000001"
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
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$url = '/api-pay/remit/v1.1/balanceQuery';
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				$url
			);
			$request['data'] = [
				'partnerReferenceNo' => $transaction->getReferenceNumber() ?? date_format($now, 'YmdHis'),
				'accountNo' => $this->getID(),
				'balanceTypes' => 'CASH',
				// 'additionalInfo' => [],
			];
			//
			$payload = implode(':', [
				'POST',
				$url,
				strtolower(hash('sha256', json_encode($request['data']))),
				$time
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->getID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => '-',
				'CHANNEL-ID' => '-',
			];
			$request['options'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success check balance
						{
							"referenceNo": "2024062043090000006",
							"accountNo": "2406040000010430",
							"name": "Test Merchant",
							"partnerReferenceNo": "20240620100506",
							"responseMessage": "Request has been processed successfully",
							"accountInfos": {
								"amount": {
										"value": 0,
										"currency": "Rp"
								},
								"holdAmount": {
										"value": 0,
										"currency": "Rp"
								},
								"floatAmount": {
										"value": 0,
										"currency": "Rp"
								},
								"ledgerBalance": {
										"value": 5000000,
										"currency": "Rp"
								},
								"availableBalance": {
										"value": 5000000,
										"currency": "Rp"
								},
								"currentMultilateralLimit": {
										"value": 0,
										"currency": "Rp"
								},
								"status": "Available",
								"balanceType": "Cash",
								"registrationStatusCode": "registered"
							},
							"responseCode": "SUCCESS"
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
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$url = '/api-pay/remit/v1.1/remitInquiry';
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				$url
			);
			$request['data'] = [
				'partnerReferenceNo' => $transaction->getReferenceNumber() ?? date_format($now, 'YmdHis'),
				'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
				'beneficiaryBankCode' => $transaction->getCustomerBankCode(),
				'additionalInfo' => [
					'remitType' => 'BANK',
					'amount' => $transaction->getAmount(),
				],
			];
			//
			$payload = implode(':', [
				'POST',
				$url,
				strtolower(hash('sha256', json_encode($request['data']))),
				$time
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->getID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => '-',
				'CHANNEL-ID' => '-',
			];
			$request['options'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success bank account inquiry
						{
							"beneficiaryAccountNo": "7700173383",
							"beneficiaryAccountName": "7700173383",
							"referenceNo": "2024062043090000014",
							"beneficiaryBankName": "BANK DANAMON",
							"partnerReferenceNo": "20240620101331",
							"currency": "Rp",
							"beneficiaryBankCode": "011",
							"responseMessage": "Request has been processed successfully",
							"responseCode": "SUCCESS"
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
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$url = '/api-pay/remit/v1.1/remitCreate';
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				$url
			);
			$request['data'] = [
				'partnerReferenceNo' => $transaction->getReferenceNumber() ?? date_format($now, 'YmdHis'),
				'feeType' => 'OUR',
				'currency' => 'Rp',
				'amount' => [
					'value' => (float) $transaction->getAmount(),
					'currency' => 'Rp',
				],
				'customerReference' => $transaction->getTransactionID(),
				'beneficiaryAccountNo' => $transaction->getCustomerBankAccountNumber(),
				'beneficiaryEmail' => $transaction->getCustomerEmail(),
				'transactionDate' => $transaction->getTransactionDate(),
				'sourceAccountNo' => $transaction->getParam('SOURCE_ACCOUNT_NUMBER'),
				'remark' => $transaction->getRemark(),
				// 'additionalInfo' => [],
				// 'remitType' => [],
			];
			//
			$payload = implode(':', [
				'POST',
				$url,
				strtolower(hash('sha256', json_encode($request['data']))),
				$time
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->getID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => '-',
				'CHANNEL-ID' => '-',
			];
			$request['options'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success disb
						{
							"beneficiaryAccountNo": "7700173383",
							"amount": {
								"currency": "Rp",
								"value": 10000
							},
							"referenceNo": "2024062043090000020",
							"partnerReferenceNo": "20240620180123",
							"beneficiaryBankCode": "011",
							"responseMessage": "Request has been processed successfully",
							"sourceAccountNo": "010430",
							"responseCode": "SUCCESS"
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
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$time = date_format($now, 'Y-m-d\TH:i:s.v\Z');
			$url = '/api-pay/remit/v1.1/remitQuery';
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				$url
			);
			$request['data'] = [
				'originalPartnerReferenceNo' => $transaction->getReferenceNumber(),
				'originalReferenceNo' => $transaction->getOriginalReferenceNumber(),
				'originalExternalId' => $transaction->getExternalID(),
				'serviceCode' => 'remit.query',
				'transactionDate' => $transaction->getTransactionDate(),
				'amount' => [
					'value' => (float) $transaction->getAmount(),
					'currency' => 'Rp',
				],
				// 'additionalInfo' => [],
			];
			//
			$payload = implode(':', [
				'POST',
				$url,
				strtolower(hash('sha256', json_encode($request['data']))),
				$time
			]);
			$signature = '';
			$private_key = $this->RetrievePrivateKey();
			$private_key = openssl_pkey_get_private($private_key);
			if (!openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
				throw new \Exception('Failed to getToken', 800);
			}
			//
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'X-TIMESTAMP' => $time,
				'X-SIGNATURE' => base64_encode($signature),
				'X-PARTNER-ID' => $this->getID(),
				'X-EXTERNAL-ID' => date_format($now, 'YmdHis'),
				'ORIGIN' => '-',
				'CHANNEL-ID' => '-',
			];
			$request['options'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->responseCode)
					&& trim(strtoupper($content->responseCode)) == 'SUCCESS'
				) {
					/* // Success check status
						{
							"beneficiaryAccountNo": "7700173383",
							"amount": {
								"currency": "Rp",
								"value": 10000
							},
							"originalReferenceNo": "2024062043090000025",
							"referenceNumber": "20240620110823",
							"originalExternalId": "20240620110823",
							"serviceCode": "18",
							"latestTransactionStatus": "SUCCESS",
							"transactionStatusDesc": "Successful",
							"originalPartnerReferenceNo": "20240620180823",
							"responseMessage": "Request has been processed successfully",
							"sourceAccountNo": "010430",
							"responseCode": "SUCCESS"
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