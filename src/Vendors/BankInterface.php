<?php

namespace IbIntegrator\Vendors;

interface BankInterface
{

	/**
		Bank Account Collection & Statement Services:

		- Check Account Balance
		- View Account Statement (Mutasi)
		- Bank Account Inquiry:
			- In-house
			- Interbank
		- Get Available Bank List for Transfer
		- Do Transfer:
			- In-house/Overbook
			- Interbank
				- Online Transfer
				- RTGS
				- SKN/Clearing
				- BIFast
		- Check Transfer Status
		- Billing Payment:
			- Get Biller Institution List
			- Billing Inquiry
			- Pay Bill
	 */
	/*--------------------------------------  // Instruction  -------------------------------------------------------*/
	public function GenerateSignature($args = []);
	public function GetToken($args = []);
	//
	public function CheckAccountBalance(Transaction $transaction);
	public function ViewAccountStatement(Transaction $transaction);
	public function ViewTransactionHistory(Transaction $transaction);
	//
	public function AccountInquiryInHouse(Transaction $transaction);
	public function AccountInquiryInterbank(Transaction $transaction);
	//
	public function GetBankList(Transaction $transaction);
	public function TransferInHouse(Transaction $transaction);
	public function CheckInHouseTransferStatus(Transaction $transaction);
	public function TransferInterbank(Transaction $transaction);
	public function CheckInterbankTransferStatus(Transaction $transaction);

}
