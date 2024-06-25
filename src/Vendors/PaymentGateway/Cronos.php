<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\PaymentGateway\PaymentGatewayInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class Cronos extends Vendor implements PaymentGatewayInterface
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
			$payment_method = trim(strtolower($transaction->getPaymentMethod()));
			$payment_channel = trim(strtolower($transaction->getPaymentChannel()));
			switch ($payment_method) {
				case 'va':
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						'/virtual-account'
					);
					$request['data'] = [
						'bankCode' => strval($payment_channel),
						'singleUse' => true,
						'type' => 'ClosedAmount',
						'reference' => $transaction->getReferenceNumber(),
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->getCallbackURL(),
						],
					];
					break;
				case 'qris':
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						'/qris'
					);
					$request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->getCallbackURL(),
						],
					];
					break;
				case 'ewallet':
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						'/e-wallet'
					);
					$request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'phoneNumber' => $transaction->getCustomerPhone(),
						'channel' => $payment_channel,
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->getCallbackURL(),
							'successRedirectUrl' => $this->getReturnURL(),
						],
					];
					break;
				case 'cstore':
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						'/retail'
					);
					$request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'phoneNumber' => $transaction->getCustomerPhone(),
						'channel' => $payment_channel,
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->getCallbackURL(),
						],
					];
					break;
				case 'cc':
					$request['url'] = CleanURL(
						$this->getPaymentURL() .
						'/credit-card'
					);
					$request['data'] = [
						'reference' => $transaction->getReferenceNumber(),
						'phoneNumber' => $transaction->getCustomerPhone(),
						'amount' => (int) $transaction->getAmount(),
						'expiryMinutes' => (int) $transaction->getExpireIn(), // minutes
						'viewName' => $transaction->getCustomerName(),
						'additionalInfo' => [
							'callback' => $this->getCallbackURL(),
						],
					];
					break;
				default:
					throw new \Exception('Undefined payment method', 800);
					break;
			}
			//
			$body_data = $request['data'];
			$json_encoded = json_encode($body_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
			$string_to_hash = $this->getID() . $json_encoded;
			$merchant_token = $this->getMerchantToken();
			$hash_hmac_sha512 = hash_hmac('sha512', $string_to_hash, $merchant_token);
			//
			$request['headers'] = [
				'On-Key' => $this->getID(),
				'On-Token' => $this->getMerchantToken(),
				'On-Signature' => $hash_hmac_sha512,
				'Accept' => 'application/json',
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
					isset($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == 'success'
				) {
					/* // Success QRIS
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "aeac88d3-2cbb-4fdd-ade7-d6692b169b43",
								"merchantRef": "1719307879",
								"status": "pending",
								"feePayer": "customer",
								"amount": 15000,
								"fee": 150,
								"totalAmount": 15150,
								"expiredDate": "2024-06-25T16:41:20+07:00",
								"paidDate": null,
								"settleDate": "2024-06-25T16:31:20+07:00",
								"additionalInfo": {
										"callback": "http:\/\/tester.com\/secure\/callback\/demo"
								},
								"qris": {
										"content": "Omnis exercitationem velit odit suscipit. Soluta eligendi dolor non sed facilis tempore. Excepturi et ad sit incidunt. Nobis cum voluptates ipsam non.",
										"image": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAe8AAAHvCAYAAAB9iVfNAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAN0ElEQVR4nO3cwXLbMLZF0edX+f9fVk8zcKXRMRjcDa41dlmURHEXJufr8\/n8HwDQ8f+nLwAA+N+INwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxPx66h9\/fX19nvrfZZ\/P52vl7059fqeub\/V1V91yfdO\/j+m\/87e9j1XT779b7P7efufkDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMeINADGPLaytenKB5l+avjB06nN+2+uu2r1c9bYltrd9v6t23y+7Tf\/eVk143jt5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMzxhbVVpxZt3rZENH2hacKy0Z\/cspy2+\/pueR\/T779bvO15\/zecvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASAms7DG904tEe1+3enLRtOvb9X0ZbxbPufpfB99Tt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQY2FtqN0LSKf+327Tr2\/3607\/f7udWu47tTg2\/X5mLidvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiMksrO1eNrrFLQtcuxeuTt0v0xe9Vk1f5LtlmWz6fXDK297v33DyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJjjC2vTF5CmO7XoNX2Ba\/r73W369a1y\/zX\/3yrP+32cvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW16UtO\/Mz073f6ctVu05fdTv2\/6W5ZTpv+PLiRkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEPLawdsvS1O73cer6vO7P\/u7U\/XzL4tj03+Up7r+fmX5fPbk85+QNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMY8trO12aonoyYWcf\/m6p5bJdrtlUeltr7tq93166v+tmn59q275\/KZ\/zr9z8gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoCYzMLa7iWd3U5d36nFolW3fB\/TP5dT73f673L3607\/\/fK96ffp33DyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJivz+fswM+Nyzc7TP9cTi2OWTr7N\/9v1S2LYz6\/773t+bJqwvPZyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiHltYswz1M9OXjd72+Z1a\/lp16ne02\/T3ccvzatX0+2XVjQudTt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ8+v0Bew2fQHplsWiU6YvJZ1adnvbouEtr7v7cz61kHjqvp\/+3H3y\/nPyBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgJivz+euwa\/py2mnFp9uWRg69TlPXxy7Zblv+v0y\/Xc0\/T6Y\/vwrPe+dvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASAms7A2feFq1fSFplXTl5xWTV\/02m369a2a\/jyY\/vuYft9P\/34ncPIGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAmF9P\/eNTCzmnlo2mLyqdcsui0u7re9v7XfW25bnpz7Xppt\/3T97PTt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ8\/X5vHO459Si0qpT13fLMp7Ppfm6\/Mzu58bbnkOrJvTDyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBifp2+gN3etgh0y+uuOrUMZSHse9Pvl1XT38ep+\/Rtz8nS79zJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGK+Pp9nBoPetnS2ysLQLNMXs6Z\/b+7n7+2+vlsW6la5r\/47J28AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIOb6wtmr6gs8ppxaapi9Dnbpfpn8up\/j9fu\/U8tctn9+qCYtouzl5A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQMyvp\/7xLYs2p97H6gLS9OUli08\/M\/3+O\/W6p5bJTpn++91t+vc24fqcvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASDmsYW1VdMXwlZNWNz5k+nXN33harrp3++q6b\/zVaeWzk59frfcf9Pvq985eQNAjHgDQIx4A0CMeANAjHgDQIx4A0CMeANAjHgDQIx4A0DM8YW1W9yyTHZqKWn6stH0Zai33X+rfG\/\/xi3vo8TJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGK+Pp9nhq12L3VNX+A65dSy0fTvY\/ry1y3cfxSc6syTvw8nbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIj59dQ\/PrW8dOp1V+1enjvllgWz6Utdu5ehTn1vp97H9N+b6\/uZ1eu7cfHTyRsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBiHltYm774dMr0havdr7vb7vcx\/XM59f+mL\/ytmr78Nf3+u2UZb3oX\/oaTNwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMQ8trA2fflm+oLU9EWg6Z\/fLaYvcJ163emLXrd42\/c2\/bn7OydvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHlsYe2W5ZvpizunFrimL3\/d8rmsmr6Etdv03+WqWxYmT90v0\/\/fk5y8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOaxhbXpy2m7nXq\/t3x+t7jl+9h9P59awjq19Dj989vtlvc7fenxd07eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEPPYwtrbloimL\/NM\/z4s1P3M2xYNp7+P6de326nny27Tr+93Tt4AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQ89jC2m6nlm9OLZ2d+n+n3PK5TF9omr5AuPv6Tr3fU255\/p1aepy+5Pk7J28AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIOb6wdmoRaPV1py+YvW0Bbvr73W36MtSEpal\/afqC3qrpC2b8d07eABAj3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AEHN8YW23U0tTq04tmJ16v6cW0VZf922LfLtNX+A6dX2n3u\/0Rb7d3rwY6OQNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMY8trE1fqpm+lLTqbctpu\/\/fqSW26b+PW7ztcz71HLrldUu\/cydvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiHlsYW3VqWWe3XYv80xY8PmTU9c3fdlt+kLTqeW5Vad+R6umLy5Od8si5ARO3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABDz2MLa9AWp3d62qHTLstv0Ra9b7ufpC1fTl79uuQ\/e9px8kpM3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxDy2sDZ9sWj66+5e9Jq+cHXKqSWx6d\/b9EWv6ctk0+8D91+fkzcAxIg3AMSINwDEiDcAxIg3AMSINwDEiDcAxIg3AMSINwDEPLawZiHne6uLRacWpKYv1O1enrvF9MWsW0z\/vd2yjLdq+pLdk5y8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOaxhbVVpUWbP9m9bGQh7HvT75dT39stn8upZcFVp5YAp3+\/p65v+rLbk5y8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIEa8ASBGvAEgRrwBIOb4wtqq6YtKp1731JLT7v83YbHoT07df29b2tvt1P28avqi3PT7b\/pz40lO3gAQI94AECPeABAj3gAQI94AECPeABAj3gAQI94AECPeABCTWVhjllPLRqeWsKY7tSR26v+tumU5bff1Tf8d7V6O3G3C88XJGwBixBsAYsQbAGLEGwBixBsAYsQbAGLEGwBixBsAYsQbAGIsrA11y3LVbqfe76pTS1irpi9\/7Tb9+m4xfclu1annxt9w8gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoCYzMKapaSfObVsNH2p620LddNfd\/qC3qrpn\/Pu\/3fL86XUGSdvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiBFvAIgRbwCIEW8AiDm+sDZ9KWm6U8tVuxeLdt8Htyx1TV+ueptbltNW7b7\/bllEm7Ac6eQNADHiDQAx4g0AMeINADHiDQAx4g0AMeINADHiDQAx4g0AMV+fjyElAChx8gaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAGPEGgBjxBoAY8QaAmP8A35gKYsiGTCoAAAAASUVORK5CYII=",
										"url": ""
								}
							}
						}
					*/
					/* // Success VA
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "4dd6daf1-50bc-4508-af26-e57da5bffbdf",
								"merchantRef": "1719307920",
								"status": "pending",
								"feePayer": "customer",
								"amount": 15000,
								"fee": 5000,
								"totalAmount": 20000,
								"expiredDate": "2024-06-25T16:42:01+07:00",
								"paidDate": null,
								"settleDate": "2024-06-25T16:32:01+07:00",
								"additionalInfo": {
										"callback": "http:\/\/tester.com\/secure\/callback\/demo"
								},
								"virtualAccount": {
										"bankCode": "014",
										"vaNumber": "036709208150",
										"viewName": "Testing"
								}
							}
						}
					*/
					/* // Success EWALLET
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "506ae055-a75a-4a0f-a562-8ff599611a62",
								"merchantRef": "1719307998",
								"status": "pending",
								"feePayer": "customer",
								"amount": 15000,
								"fee": 450,
								"totalAmount": 15450,
								"expiredDate": "2024-06-25T16:43:19+07:00",
								"paidDate": null,
								"settleDate": "2024-06-25T16:33:19+07:00",
								"additionalInfo": {
										"callback": "http:\/\/tester.com\/secure\/callback\/demo"
								},
								"eWallet": {
										"viewName": "Testing",
										"channel": "ovo",
										"url": "http:\/\/www.wilderman.info\/atque-non-non-commodi-atque-praesentium-iusto-vero.html"
								}
							}
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
			// Go
			$request['url'] = CleanURL(
				$this->getRequestURL() . '/check/' .
				$transaction->getParam('id') .
				'?resendCallback=' .
				$transaction->getParam('resend_callback')
			);
			$request['headers'] = [
				'On-Key' => $this->getID(),
				'On-Token' => $this->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->getID(), $this->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$get = $this->DoRequest('GET', $request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					isset($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == 'success'
				) {
					/* // Success inquiry QRIS
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "4dd6daf1-50bc-4508-af26-e57da5bffbdf",
								"merchantRef": "1719307920",
								"status": "pending",
								"feePayer": "customer",
								"amount": 15000,
								"fee": 5000,
								"totalAmount": 20000,
								"expiredDate": "2024-06-25T16:42:01+07:00",
								"paidDate": null,
								"settleDate": "2024-06-25T16:32:01+07:00",
								"additionalInfo": {
										"callback": "http:\/\/tester.com\/secure\/callback\/demo"
								},
								"virtualAccount": {
										"bankCode": "014",
										"vaNumber": "036709208150",
										"viewName": "Testing"
								}
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
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/balance'
			);
			$request['headers'] = [
				'On-Key' => $this->getID(),
				'On-Token' => $this->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->getID(), $this->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$get = $this->DoRequest('GET', $request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					isset($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == 'success'
				) {
					/* // Success check balance
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"active": 1933280,
								"pending": 0,
								"total": 1933280
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
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/account-inquiry'
			);
			$request['data'] = [
				'bankCode' => $transaction->getCustomerBankCode(),
				'accountNumber' => $transaction->getCustomerBankAccountNumber(),
				'reference' => $transaction->getReferenceNumber(),
				'additionalInfo' => [
					'callback' => $this->getCallbackURL(),
				],
			];
			//
			$body_data = $request['data'];
			$json_encoded = json_encode($body_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
			$string_to_hash = $this->getID() . $json_encoded;
			$merchant_token = $this->getMerchantToken();
			$hash_hmac_sha512 = hash_hmac('sha512', $string_to_hash, $merchant_token);
			//
			$request['headers'] = [
				'On-Key' => $this->getID(),
				'On-Token' => $this->getMerchantToken(),
				'On-Signature' => $hash_hmac_sha512,
				'Accept' => 'application/json',
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
					isset($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == 'success'
				) {
					/* // Success bank account inquiry
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "9a1ad912-13a0-4a59-8de3-76ec0b278330",
								"status": "pending",
								"accountNumber": "7700173383",
								"accountName": "Ms. Luisa Berge",
								"bankCode": "011",
								"additionalInfo": {
										"callback": "http:\/\/tester.com\/secure\/callback\/demo"
								}
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
			$request['url'] = CleanURL(
				$this->getRequestURL() .
				'/disburse'
			);
			$request['data'] = [
				'bankCode' => $transaction->getCustomerBankCode(),
				'recipientAccount' => $transaction->getCustomerBankAccountNumber(),
				'reference' => $transaction->getReferenceNumber(),
				'amount' => (int) $transaction->getAmount(),
				'additionalInfo' => [
					'callback' => $this->getCallbackURL(),
				],
			];
			//
			$body_data = $request['data'];
			$json_encoded = json_encode($body_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
			$string_to_hash = $this->getID() . $json_encoded;
			$merchant_token = $this->getMerchantToken();
			$hash_hmac_sha512 = hash_hmac('sha512', $string_to_hash, $merchant_token);
			//
			$request['headers'] = [
				'On-Key' => $this->getID(),
				'On-Token' => $this->getMerchantToken(),
				'On-Signature' => $hash_hmac_sha512,
				'Accept' => 'application/json',
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
					isset($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == 'success'
				) {
					/* // Success disb
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "1b367741-145b-4228-96ba-fdd1abf18327",
								"merchantRef": "1719308702",
								"status": "pending",
								"feePayer": "customer",
								"amount": 10500,
								"fee": 3000,
								"totalAmount": 7500,
								"expiredDate": "2024-06-25T17:45:05+07:00",
								"paidDate": null,
								"settleDate": "2024-06-25T16:45:05+07:00",
								"additionalInfo": {
										"callback": "http:\/\/tester.com\/secure\/callback\/demo"
								},
								"disbursement": {
										"bankCode": "011",
										"recipientAccount": "7700173383",
										"recipientName": null
								}
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
			// Go
			$request['url'] = CleanURL(
				$this->getRequestURL() . '/check/' .
				$transaction->getParam('id') .
				'?resendCallback=' .
				$transaction->getParam('resend_callback')
			);
			$request['headers'] = [
				'On-Key' => $this->getID(),
				'On-Token' => $this->getMerchantToken(),
				'On-Signature' => hash_hmac('sha512', $this->getID(), $this->getMerchantToken()),
				'Accept' => 'application/json',
			];
			$get = $this->DoRequest('GET', $request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					isset($content->responseMessage)
					&& trim(strtolower($content->responseMessage)) == 'success'
				) {
					/* // Success check status
						{
							"responseCode": 200,
							"responseMessage": "success",
							"responseData": {
								"id": "ed2e4a12-5997-40a8-8e7e-9591679de41e",
								"merchantRef": "1719308808",
								"status": "pending",
								"feePayer": "customer",
								"amount": 10500,
								"fee": 3000,
								"totalAmount": 7500,
								"expiredDate": "2024-06-25T17:46:49+07:00",
								"paidDate": null,
								"settleDate": "2024-06-25T16:46:49+07:00",
								"additionalInfo": {
										"callback": "http:\/\/tester.com\/secure\/callback\/demo"
								},
								"disbursement": {
										"bankCode": "011",
										"recipientAccount": "7700173383",
										"recipientName": null
								}
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