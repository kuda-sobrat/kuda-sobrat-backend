<?php

namespace App\Exceptions\Api;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception
{
    protected $statusCode;
    protected $errorCode;
    protected $errors;

    public function __construct(
        string $message = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        int $errorCode = 0,
        array $errors = []
    )
    {
        parent::__construct($message);

        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
