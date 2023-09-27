<?php

namespace IbIntegrator\Exceptions;

interface ExceptionInterface extends \Throwable
{

	public function getErrorCode();
	public function getErrorMessage();

}
