<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\PaymentGatewayInterface;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;
use IbIntegrator\Exceptions\RequestorException;

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
		// try {
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
// print_r($request);
			$post = $this->DoRequest('POST', $request);
print_r($post);
exit();
			if ($post) {
				if ($post) {
					$res = [
						'status' => '000',
						'data' => (array) $content->responseData,
					];
				} else {
					throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ? json_encode($content) : 'Unknown status']), 901);
				}
			} else {
				throw new \Exception(implode(': ', [__FUNCTION__ . '() failed', $content ?? 'Unknown error']), 902);
			}
		// } catch (\Throwable $e) {
			// throw new RequestorException($e, __FUNCTION__);
			// $error = ErrorString($e, __FUNCTION__);
			// throw new \Exception($error, $e->getCode());
		// 	$this->ThrowErrorException($e);
		// }
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