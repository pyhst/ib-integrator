<?php

namespace IbIntegrator\Vendors\Switching;

use IbIntegrator\Vendors\Transaction;

interface SwitchingInterface
{

	/*--------------------------------------  // Instruction  -------------------------------------------------------*/
	public function GenerateSignature($args = []);
	public function AuthGetToken($args = []);
	//
	// Billing
	public function CreateBilling(Transaction $transaction);
	public function InquiryBilling(Transaction $transaction);
	public function CancelBilling(Transaction $transaction);
	//
	// Payment
	public function MakePayment(Transaction $transaction);
	public function InquiryPayment(Transaction $transaction);
	public function CancelPayment(Transaction $transaction);
	//
	// Refund
	public function MakeRefund(Transaction $transaction);
	public function InquiryRefund(Transaction $transaction);
	public function CancelRefund(Transaction $transaction);
	//
	// Callbacks
	public function ReceivePaymentCallback($request);
	public function ReceiveRefundCallback($request);

}
