<?php

namespace IbIntegrator\Vendors\Switching;

use IbIntegrator\Vendors\Transaction;

interface SwitchingInterface
{

	/*--------------------------------------  // Instruction  -------------------------------------------------------*/
	public function GenerateSignature($args = []);
	public function AuthGetToken($args = []);
	//
	public function CreateBilling(Transaction $transaction);
	public function InquiryBilling(Transaction $transaction);
	//
	public function MakePayment(Transaction $transaction);
	public function InquiryPayment(Transaction $transaction);
	public function CancelPayment(Transaction $transaction);
	//
	public function MakeRefund(Transaction $transaction);
	public function InquiryRefund(Transaction $transaction);
	public function CancelRefund(Transaction $transaction);
	//
	public function ReceivePaymentCallback($request);
	public function ReceiveRefundCallback($request);

}
