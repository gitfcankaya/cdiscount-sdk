<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Exception;

/**
 * Base exception for CDiscount SDK
 */
class SdkException extends \Exception
{
    /** @var array|null */
    protected $details;

    /** @var string|null */
    protected $traceId;

    /**
     * SdkException constructor
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param array|null $details
     * @param string|null $traceId
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        ?array $details = null,
        ?string $traceId = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
        $this->traceId = $traceId;
    }

    /**
     * Get error details
     *
     * @return array|null
     */
    public function getDetails(): ?array
    {
        return $this->details;
    }

    /**
     * Get trace ID
     *
     * @return string|null
     */
    public function getTraceId(): ?string
    {
        return $this->traceId;
    }
}
