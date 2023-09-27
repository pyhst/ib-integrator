<?php

namespace IbIntegrator\Vendors\Bank;

use IbIntegrator\Vendors\Bank\BankInterface;
use IbIntegrator\Vendors\Vendor;
use IbIntegrator\Vendors\Transaction;
use IbIntegrator\Vendors\Requestor;

use IbIntegrator\Exceptions\ErrorException;
use IbIntegrator\Exceptions\JsonException;

class Cimb extends Vendor implements BankInterface
{

	public function GenerateSignature($args = [])
	{

	}

	public function GetToken($args = [])
	{

	}

	//

	public function CheckAccountBalance(Transaction $transaction)
	{

	}

	public function ViewAccountStatement(Transaction $transaction)
	{

	}

	public function ViewTransactionHistory(Transaction $transaction)
	{

	}

	//

	public function AccountInquiryInHouse(Transaction $transaction)
	{

	}

	public function AccountInquiryInterbank(Transaction $transaction)
	{

	}

	//

	public function GetBankList(Transaction $transaction)
	{

	}

	public function TransferInHouse(Transaction $transaction)
	{

	}

	public function CheckInHouseTransferStatus(Transaction $transaction)
	{

	}

	public function TransferInterbank(Transaction $transaction)
	{

	}

	public function CheckInterbankTransferStatus(Transaction $transaction)
	{

	}

}
