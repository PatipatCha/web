<?php

namespace MakroSdk\Exceptions;

class MakroSDKException extends \Exception
{
    private $errors;

    public function __construct($message, $errors = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}
