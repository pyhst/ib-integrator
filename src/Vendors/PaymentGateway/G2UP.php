<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\PaymentGateway\PaymentGatewayInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class G2UP extends Vendor implements PaymentGatewayInterface
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
			$time = time();
			$timestamp = date('Y/m/d H:i:s');
			$signature = hash_hmac('sha512',
				'payment/qris-request' .
				$this->getSecret() .
				$timestamp
			, $this->getID());
			$request['url'] = CleanURL(
				$this->getPaymentURL() .
				'/payment/qris-request'
			);
			$request['data'] = [
				'topUpAmount' => (float) $transaction->getAmount(),
				'memberNo' => $transaction->getCustomerID(),
				'referenceNo' => $transaction->getReferenceNumber(),
			];
			$request['headers'] = [
				'Accept' => 'application/json',
				'AppKey' => $this->getID(),
				'AppSecret' => $this->getSecret(),
				'X-TIME' => $timestamp,
				'X-SIGNATURE' => $signature,
			];
			$request['option'] = [
				'as_json' => true,
			];
			$post = $this->DoRequest('POST', $request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status)
					&& trim(strtolower($content->status)) == "success"
				) {
					/* // Success // QRIS
						{
							"status": "success",
							"message": "QRIS Merchant",
							"qrcode": "00020101021226670016ID.CO.MBAYAR.WWW01189360082931683196300214E2P168319630630303UMI51460016ID.CO.MBAYAR.WWW0215ID10210651519170303UMI520407425303360540815000.005802ID5910Risang Pay6015JAKARTA SELATAN6105121506234050622479607062110330810Risang Pay6304477C"
						}
					*/
					$res = [
						'status' => '000',
						'data' => array_merge((array) $content, $request['data']),
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
			$time = time();
			$timestamp = date('Y/m/d H:i:s');
			$signature = hash_hmac('sha512',
				'payment/qris-request' .
				$this->getSecret() .
				$timestamp
			, $this->getID());
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/qris/inquiry'
			);
			$request['data'] = [
				'startDate' => $transaction->getStartDate(),
				'endDate' => $transaction->getEndDate(),
				'referenceNo' => $transaction->getReferenceNumber(),
				'memberNo' => $transaction->getCustomerID(),
			];
			$request['headers'] = [
				'Accept' => 'application/json',
				'X-TIME' => $timestamp,
				'X-SIGNATURE' => $signature,
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
					!empty($content->referenceNo)
					&& !empty($content->paymentNo)
				) {
					/* // Success inquiry QRIS
						[
							{
								"referenceNo": "1716267137",
								"memberNo": "TESTER",
								"topUpAmount": 15000,
								"requestDate": "2024-05-21 11:52:20",
								"responseDate": "2024-05-21 11:52:21",
								"paymentNo": "QRP24052100001",
								"paymentDate": "2024-05-21 11:56:54",
								"paymentStatus": "00"
							}
						]
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
		// Not applicable
	}

	public function RefundPayment(Transaction $transaction)
	{
		// Not applicable
	}

	public function PaymentCallback($request)
	{
		/* // Example incoming data
			{
				"referenceNo":"R015",
				"memberNo":"001",
				"merchantID":1,
				"CustName":"CUSTOMER",
				"CustEmail":"customer@bbb.id",
				"phone":"085741907194",
				"amount":100,
				"status":"PAID",
				"paydate":"2024-05-09 18:08:43"
			}
		*/
		try {
			ValidateArgs((object) $request, [
				'referenceNo',
				'amount',
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
		// Not applicable
	}

	public function BankAccountInquiry(Transaction $transaction)
	{
		// Not applicable
	}

	public function FundTransfer(Transaction $transaction)
	{
		// Not applicable
	}

	public function CheckFundTransferStatus(Transaction $transaction)
	{
		// Not applicable
	}

	public function DisbursementCallback($request)
	{
		// Not applicable
	}

}