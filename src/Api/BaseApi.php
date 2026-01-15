<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Http\HttpClient;
use CDiscount\Sdk\Response\ApiResponse;

/**
 * Base API class
 */
abstract class BaseApi
{
    /** @var HttpClient */
    protected $httpClient;

    /**
     * BaseApi constructor
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Build query parameters, removing null values
     *
     * @param array $params
     * @return array
     */
    protected function buildQueryParams(array $params): array
    {
        return array_filter($params, function ($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * Build pagination parameters
     *
     * @param int|null $pageIndex
     * @param int|null $pageSize
     * @param string|null $cursor
     * @param int|null $limit
     * @return array
     */
    protected function buildPaginationParams(
        ?int $pageIndex = null,
        ?int $pageSize = null,
        ?string $cursor = null,
        ?int $limit = null
    ): array {
        $params = [];

        if ($cursor !== null) {
            $params['cursor'] = $cursor;
        }

        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        if ($pageIndex !== null) {
            $params['pageIndex'] = $pageIndex;
        }

        if ($pageSize !== null) {
            $params['pageSize'] = $pageSize;
        }

        return $params;
    }

    /**
     * Build date range parameters
     *
     * @param \DateTimeInterface|string|null $min
     * @param \DateTimeInterface|string|null $max
     * @param string $minKey
     * @param string $maxKey
     * @return array
     */
    protected function buildDateRangeParams(
        $min,
        $max,
        string $minKey = 'updatedAtMin',
        string $maxKey = 'updatedAtMax'
    ): array {
        $params = [];

        if ($min !== null) {
            $params[$minKey] = $this->formatDateTime($min);
        }

        if ($max !== null) {
            $params[$maxKey] = $this->formatDateTime($max);
        }

        return $params;
    }

    /**
     * Format date time for API
     *
     * @param \DateTimeInterface|string $dateTime
     * @return string
     */
    protected function formatDateTime($dateTime): string
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime->format('c');
        }

        return $dateTime;
    }

    /**
     * Build comma-separated list from array
     *
     * @param array|string|null $value
     * @return string|null
     */
    protected function buildCommaSeparatedList($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return implode(',', $value);
        }

        return $value;
    }

    /**
     * Get HTTP client
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
}
