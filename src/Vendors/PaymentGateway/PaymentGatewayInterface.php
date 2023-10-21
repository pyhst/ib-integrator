<?php

namespace IbIntegrator\Vendors\PaymentGateway;

use IbIntegrator\Vendors\Transaction;

interface PaymentGatewayInterface
{

	/*--------------------------------------  // Instruction  -------------------------------------------------------*/
	public function GenerateSignature($args = []);
	public function AuthGetToken($args = []);
	//
	public function CreateBilling(Transaction $transaction);
	public function InquiryPayment(Transaction $transaction);
	public function CancelPayment(Transaction $transaction);
	public function RefundPayment(Transaction $transaction);
	public function PaymentCallback($request);
	//
	public function GetBankList(Transaction $transaction);
	public function CheckAccountBalance(Transaction $transaction);
	public function BankAccountInquiry(Transaction $transaction);
	public function FundTransfer(Transaction $transaction);
	public function CheckFundTransferStatus(Transaction $transaction);
	public function DisbursementCallback($request);

}
