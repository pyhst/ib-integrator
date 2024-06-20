<?php

namespace IbIntegrator\Vendors\Switching;

use IbIntegrator\Vendors\Switching\SwitchingInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class Artajasa extends Vendor implements SwitchingInterface
{

	use Requestor;
	//
	public $datetime;
	public $timestamp;
	public $his;
	public $md;
	public $mdhis;
	public $md_tomorrow;
	public $gmtmdhis;
	public $gmtdate;
	public $ymdhis;
	//
	protected $timeout = 60;

	public function __construct()
	{
		// $this->_Replace();
	}

	public function GenerateSignature($args = [])
	{
		$data_formatted = strtoupper(
				preg_replace('/\s+/', '', json_encode($args['data']))
			) . ":$this->gmtdate";
		$sign = base64_encode(
			hash_hmac('sha512', $data_formatted, $args['secret_key'], true)
		);
		return $sign;
	}

	public function AuthGetToken($args = [])
	{
		// Not applicable
	}

	public function _Replace($data = null)
	{
		// Set post variables
		$this->datetime = new \DateTime(now(), new \DateTimeZone('UTC'));
		$this->timestamp = strval( $this->datetime->getTimestamp() );
		$this->his = $this->datetime->format('His');
		$this->md = $this->datetime->format('md');
		$this->mdhis = $this->datetime->format('mdHis');
		$this->ymdhis = $this->datetime->format('YmdHis');
		$this->md_tomorrow = $this->datetime->modify('+1 day')->format('md');
		//
		$this->datetime->modify('-1 day')->modify('-7 hours');
		$this->gmtmdhis = $this->datetime->format('mdHis');
		$this->gmtdate = $this->datetime->format('D, d M Y H:i:s \G\M\T');
		//
		if (IsJSON($data)) $data = json_encode($data);
		$data = str_replace('#timestamp#', $this->timestamp, $data);
		$data = str_replace('#his#', $this->his, $data);
		$data = str_replace('#md#', $this->md, $data);
		$data = str_replace('#md_tomorrow#', $this->md_tomorrow, $data);
		$data = str_replace('#mdhis#', $this->mdhis, $data);
		$data = str_replace('#gmtmdhis#', $this->gmtmdhis, $data);
		$data = str_replace('#gmtdate#', $this->gmtdate, $data);
		$data = str_replace('#ymdhis#', $this->ymdhis, $data);
		//
		return $data;
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

	//

	public function MakePayment(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
				'Signature' => $this->GenerateSignature([
					'data' => $transaction->getData(),
					'secret_key' => $this->getSecret()
				]),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QRPaymentCreditRS)
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

	public function InquiryPayment(Transaction $transaction)
	{
		// Not applicable
	}

	public function CancelPayment(Transaction $transaction)
	{
		// Not applicable
	}

	//

	public function MakeRefund(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
				'Signature' => $this->GenerateSignature([
					'data' => $transaction->getData(),
					'secret_key' => $this->getSecret()
				]),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QRRefundRS)
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

	public function InquiryRefund(Transaction $transaction)
	{
		// Not applicable
	}

	public function CancelRefund(Transaction $transaction)
	{
		// Not applicable
	}

	//

	public function MakeCheckStatus(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
				'Signature' => $this->GenerateSignature([
					'data' => $transaction->getData(),
					'secret_key' => $this->getSecret()
				]),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QRCheckStatusRS)
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

	//

	public function ReceivePaymentCallback($request)
	{

	}

	public function ReceiveRefundCallback($request)
	{

	}

	//

	public function MakeInquiry(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
				'Signature' => $this->GenerateSignature([
					'data' => $transaction->getData(),
					'secret_key' => $this->getSecret()
				]),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QRCheckStatusRS)
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

	public function MakeEchoTest(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QREchoTestRS)
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

	public function MakeSignOn(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QRSignOnRS)
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

	public function MakeSignOff(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QRSignOffRS)
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

	public function MakeQRInquiry(Transaction $transaction)
	{
		try {
			$request['url'] = CleanURL(
				$this->getHostURL() .
				$transaction->getURL()
			);
			$request['data'] = $transaction->getData();
			$request['headers'] = [
				'Content-Type' => 'application/json',
				'Date' => $this->_Replace('#gmtdate#'),
				'Signature' => $this->GenerateSignature([
					'data' => $transaction->getData(),
					'secret_key' => $this->getSecret()
				]),
			];
			$request['options'] = [
				'as_json' => true,
				'timeout' => $this->timeout,
			];
			$post = $this->DoRequest('POST', $request);
			extract($post);
			extract($response);
			if (!empty($content) && IsJSON($content)) {
				$content = (object) json_decode($content);
				if (
					!empty($content->QRInquiryRS)
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

}
