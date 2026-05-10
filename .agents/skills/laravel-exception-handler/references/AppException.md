<?php

namespace App\Exceptions;

use Exception;
use ErrorCode;

class AppException extends Exception
{
    // Sử dụng Constructor Property Promotion của PHP 8
    public function __construct(
        private readonly ErrorCode $errorCode
    ) {
        // Truyền message và mã lỗi nội bộ lên class Exception cha
        parent::__construct($this->errorCode->getMessage(), $this->errorCode->getCode());
    }

    public function getErrorCode(): ErrorCode
    {
        return $this->errorCode;
    }
}
