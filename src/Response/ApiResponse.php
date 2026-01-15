<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Response;

/**
 * API Response wrapper class
 */
class ApiResponse
{
    /** @var int */
    private $statusCode;

    /** @var array|null */
    private $data;

    /** @var array */
    private $headers;

    /** @var string|null */
    private $rawBody;

    /**
     * ApiResponse constructor
     *
     * @param int $statusCode
     * @param array|null $data
     * @param array $headers
     * @param string|null $rawBody
     */
    public function __construct(
        int $statusCode,
        ?array $data = null,
        array $headers = [],
        ?string $rawBody = null
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->headers = $headers;
        $this->rawBody = $rawBody;
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
     * Get response data
     *
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * Get response headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get specific header
     *
     * @param string $name
     * @return string|null
     */
    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $name) {
                return is_array($value) ? $value[0] : $value;
            }
        }
        return null;
    }

    /**
     * Get raw response body
     *
     * @return string|null
     */
    public function getRawBody(): ?string
    {
        return $this->rawBody;
    }

    /**
     * Check if request was successful
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Get items from paginated response
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->data['items'] ?? [];
    }

    /**
     * Get items per page from paginated response
     *
     * @return int|null
     */
    public function getItemsPerPage(): ?int
    {
        return $this->data['itemsPerPage'] ?? null;
    }

    /**
     * Get total count from response
     *
     * @return int|null
     */
    public function getTotalCount(): ?int
    {
        return $this->data['total_item_count'] ?? $this->data['count'] ?? null;
    }

    /**
     * Get Link header for pagination
     *
     * @return string|null
     */
    public function getLinkHeader(): ?string
    {
        return $this->getHeader('Link');
    }

    /**
     * Parse Link header for pagination URLs
     *
     * @return array
     */
    public function getPaginationLinks(): array
    {
        $linkHeader = $this->getLinkHeader();
        if (!$linkHeader) {
            return [];
        }

        $links = [];
        $parts = explode(',', $linkHeader);

        foreach ($parts as $part) {
            if (preg_match('/<([^>]+)>;\s*rel="([^"]+)"/', trim($part), $matches)) {
                $links[$matches[2]] = $matches[1];
            }
        }

        return $links;
    }

    /**
     * Check if there is a next page
     *
     * @return bool
     */
    public function hasNextPage(): bool
    {
        $links = $this->getPaginationLinks();
        return isset($links['next']);
    }

    /**
     * Get next page URL
     *
     * @return string|null
     */
    public function getNextPageUrl(): ?string
    {
        $links = $this->getPaginationLinks();
        return $links['next'] ?? null;
    }
}
