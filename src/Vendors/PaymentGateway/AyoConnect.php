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
			$request['headers'] = [
				'Content-Type' => 'application/x-www-form-urlencoded',
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
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
			// throw $e;
		}
		return JSONResult($request, $res ?? [], $status_code ?? 400);
	}

	//

	public function CreateBilling(Transaction $transaction)
	{
		// Not applicable
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
				'transactionId' => $transaction->getTransactionID(),
			];
			// $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			// $rand = substr(str_shuffle($permitted_chars), 0, 32);
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->getToken(),
				// 'A-Correlation-ID' => $rand,
				// 'A-Correlation-ID' => $transaction->getTransactionID(),
				'A-Correlation-ID' => $transaction->getCorrelationID(),
				'A-Merchant-Code' => $this->getMerchantCode(),
			];
			$request['options'] = [
				'timeout' => 60,
			];
			$get = $this->DoRequest('GET', $request);
			extract($get);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					// && $content->code == "200"
					&& !empty($content->message)
					// && trim(strtoupper($content->message)) == "OK"
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
				'transactionId' => $transaction->getTransactionID(),
				'phoneNumber' => $transaction->getCustomerPhone(),
				'customerDetails' => [
					'ipAddress' => $transaction->getIPAddress(),
				],
				'beneficiaryAccountDetails' => [
					'accountNumber' => $transaction->getCustomerBankAccountNumber(),
					'bankCode' => $transaction->getCustomerBankCode(),
				]
			];
			// $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			// $rand = substr(str_shuffle($permitted_chars), 0, 32);
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->getToken(),
				// 'A-Correlation-ID' => $rand,
				// 'A-Correlation-ID' => $transaction->getTransactionID(),
				'A-Correlation-ID' => $transaction->getCorrelationID(),
				'A-Merchant-Code' => $this->getMerchantCode(),
				'A-Latitude' => '-6.200000',
				'A-Longitude' => '-106.816666',
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					// && $content->code == "202"
					&& !empty($content->message)
					// && trim(strtoupper($content->message)) == "OK"
				) {
					/* // Statuses
						0	Inactive - The beneficiary is added to the customer but the beneficiary details were not verified from the bank
						1	Active - The beneficiary details were verified from the bank
						2	Disabled - The beneficiary has been deleted by the user and can be added again
						3	Blocked - The beneficiary is permanently blocked by Ayoconnect. The beneficiary can't be used temporarily. If you want to unblock it, create a support ticket for unblocking the beneficiary
						4	Invalid - Invalid beneficiary account.
					 */
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
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/api/v1/bank-disbursements/disbursement'
			);
			$request['data'] = [
				'transactionId' => $transaction->getTransactionID(),
				'customerId' => $transaction->getCustomerID(),
				'beneficiaryId' => $transaction->getBeneficiaryID(),
				'amount' => (string) $transaction->getAmount() . '.00',
				'currency' => $transaction->getCurrency(),
				'remark' => $transaction->getDescription(),
			];
			// $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			// $rand = substr(str_shuffle($permitted_chars), 0, 32);
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->getToken(),
				// 'A-Correlation-ID' => $rand,
				// 'A-Correlation-ID' => $transaction->getTransactionID(),
				'A-Correlation-ID' => $transaction->getCorrelationID(),
				'A-Merchant-Code' => $this->getMerchantCode(),
				'A-Latitude' => '-6.200000',
				'A-Longitude' => '-106.816666',
				'A-IP-Address' => $transaction->getIPAddress(),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => 60,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					// && $content->code == "00"
					&& !empty($content->message)
					// && trim(strtoupper($content->message)) == "SUCCESS"
				) {
					/* // Statuses
						0	Processing
						1	Success
						2	Refunded
						3	Canceled
						4	Failed
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

	public function CheckFundTransferStatus(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				'/api/v1/bank-disbursements/status/' . $transaction->getTransactionID()
			);
			$request['data'] = [
				'transactionId' => $transaction->getTransactionID(),
				'transactionReferenceNumber' => $transaction->getReferenceNumber(),
				'beneficiaryId' => $transaction->getBeneficiaryID(),
				'customerId' => $transaction->getCustomerID(),
			];
			// $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			// $rand = substr(str_shuffle($permitted_chars), 0, 22);
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $this->getToken(),
				// 'A-Correlation-ID' => $rand,
				// 'A-Correlation-ID' => $transaction->getTransactionID(),
				'A-Correlation-ID' => $transaction->getCorrelationID(),
				'A-Merchant-Code' => $this->getMerchantCode(),
				'A-Latitude' => '-6.200000',
				'A-Longitude' => '-106.816666',
				'A-IP-Address' => $transaction->getIPAddress(),
			];
			$request['options'] = [
				'timeout' => 60,
			];
			$get = $this->DoRequest('GET', $request);
			extract($get);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->code)
					// && $content->code == "00"
					&& !empty($content->message)
					// && trim(strtoupper($content->message)) == "SUCCESS"
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

	public function DisbursementCallback($request)
	{
		//
	}

}