<?php

namespace IbIntegrator\Exceptions;

use Throwable;

interface ExceptionInterface extends Throwable
{

	public function getErrorCode();
	public function getErrorMessage();

}
