<?php

namespace App\Exceptions;

use Exception;

class EntityConflictException extends Exception
{
    /**
     * @var int code
     */
    protected $code;
    /**
     * @var string message
     */
    protected $message;
    /**
     * @var string errorInfo
     */
    public $errorInfo;

    /**
     * EntityConflictException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 409, Throwable $previous = null)
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
