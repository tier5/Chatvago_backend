<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class HttpBadRequestException extends Exception
{
    /**
     * @var code
     * @type int
     */
    protected $code;
    /**
     * @var $message
     * @type string
     */
    protected $message;
    /**
     * @var $errorInfo
     * @type string
     */
    public $errorInfo;

    /** Service Injection */
    public function __construct($message = "", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param int $code
     */
    public function setCode($code = 400)
    {
        $this->code = $code;
    }
    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
