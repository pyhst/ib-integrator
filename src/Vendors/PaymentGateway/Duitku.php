<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

use IbIntegrator\Vendors\PaymentGatewayInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

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

	}

	public function CancelPayment(Transaction $transaction)
	{

	}

	public function RefundPayment(Transaction $transaction)
	{

	}

	//

	public function GetBankList(Transaction $transaction)
	{

	}

	public function CheckAccountBalance(Transaction $transaction)
	{

	}

	public function BankAccountInquiry(Transaction $transaction)
	{

	}

	public function FundTransfer(Transaction $transaction)
	{

	}

	public function CheckFundTransferStatus(Transaction $transaction)
	{

	}

}