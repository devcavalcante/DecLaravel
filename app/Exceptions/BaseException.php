<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class BaseException extends Exception
{
    /**
     * @var mixed|string
     */
    protected $message;

    /**
     * @var int|mixed
     */
    protected $code;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = $message;
        $this->code = $code;
    }
}
