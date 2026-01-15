<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Exception;

/**
 * Exception for validation errors
 */
class ValidationException extends SdkException
{
    /** @var array */
    protected $errors;

    /**
     * ValidationException constructor
     *
     * @param string $message
     * @param array $errors
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = 'Validation failed',
        array $errors = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 400, $previous);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
