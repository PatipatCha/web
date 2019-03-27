<?php

namespace MakroSdk\Exceptions;

class SDKException extends \Exception
{
    private $errors;

    public function __construct($errors, Exception $previous = null)
    {
        $this->errors = $errors;

        parent::__construct($this->getUserMessage(), $this->getErrorCode(), $previous);
    }

    public function getErrors()
    {
        return array_get($this->errors, 'errors');
    }

    public function getErrorCode()
    {
        return array_get($this->errors, 'code');
    }

    public function getErrorMessage()
    {
        return array_get($this->errors, 'message');
    }

    public function getUserMessage()
    {
        return array_get($this->errors, 'userMessage');
    }

    public function getDeveloperMessage()
    {
        return array_get($this->errors, 'developerMessage');
    }
}
