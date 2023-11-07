<?php

namespace IbIntegrator\Vendors\Switching;

use IbIntegrator\Vendors\Bank\SwitchingInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class Artajasa extends Vendor implements SwitchingInterface
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

	}

	public function InquiryBilling(Transaction $transaction)
	{

	}

	//

	public function MakePayment(Transaction $transaction)
	{

	}

	public function InquiryPayment(Transaction $transaction)
	{

	}

	public function CancelPayment(Transaction $transaction)
	{

	}

	//

	public function MakeRefund(Transaction $transaction)
	{

	}

	public function InquiryRefund(Transaction $transaction)
	{

	}

	public function CancelRefund(Transaction $transaction)
	{

	}

	//

	public function ReceivePaymentCallback(Transaction $transaction)
	{

	}

	public function ReceiveRefundCallback(Transaction $transaction)
	{

	}

}
