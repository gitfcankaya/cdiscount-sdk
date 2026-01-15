<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Exception;

/**
 * Exception for API request failures
 */
class ApiException extends SdkException
{
    /** @var int */
    protected $statusCode;

    /** @var string|null */
    protected $responseBody;

    /**
     * ApiException constructor
     *
     * @param string $message
     * @param int $statusCode
     * @param string|null $responseBody
     * @param \Throwable|null $previous
     * @param array|null $details
     * @param string|null $traceId
     */
    public function __construct(
        string $message = '',
        int $statusCode = 0,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
        ?array $details = null,
        ?string $traceId = null
    ) {
        parent::__construct($message, $statusCode, $previous, $details, $traceId);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
    }

    /**
     * Get HTTP status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get response body
     *
     * @return string|null
     */
    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    /**
     * Create exception from API response
     *
     * @param int $statusCode
     * @param string $responseBody
     * @return static
     */
    public static function fromResponse(int $statusCode, string $responseBody): self
    {
        $data = json_decode($responseBody, true);
        $message = $data['title'] ?? $data['error_description'] ?? $data['detail'] ?? 'API request failed';
        $details = $data['errors'] ?? null;
        $traceId = $data['traceId'] ?? null;

        return new self($message, $statusCode, $responseBody, null, $details, $traceId);
    }
}
