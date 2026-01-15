<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Order Invoices API - Endpoints for order invoice management
 */
class OrderInvoicesApi extends BaseApi
{
    /**
     * Get order invoice imports
     *
     * @param int|null $pageSize
     * @param string|null $cursor
     * @return ApiResponse
     */
    public function getInvoiceImports(?int $pageSize = null, ?string $cursor = null): ApiResponse
    {
        $params = $this->buildQueryParams([
            'pageSize' => $pageSize,
            'cursor' => $cursor,
        ]);

        return $this->httpClient->get('/order-invoice-imports', $params);
    }

    /**
     * Upload invoice files for multiple orders
     *
     * @param array $files Array of file data for multipart upload
     * @return ApiResponse
     */
    public function uploadInvoiceFiles(array $files): ApiResponse
    {
        return $this->httpClient->postMultipart('/order-invoice-imports', $files);
    }

    /**
     * Get a single invoice import status
     *
     * @param string $importId
     * @return ApiResponse
     */
    public function getInvoiceImport(string $importId): ApiResponse
    {
        return $this->httpClient->get("/order-invoice-imports/{$importId}");
    }

    /**
     * Get invoice documents for a specific order
     *
     * @param string $orderId
     * @return ApiResponse
     */
    public function getOrderInvoiceDocuments(string $orderId): ApiResponse
    {
        return $this->httpClient->get("/orders/{$orderId}/invoice-documents");
    }

    /**
     * Upload invoice documents for a specific order
     *
     * @param string $orderId
     * @param array $files Array of file data for multipart upload
     * @return ApiResponse
     */
    public function uploadOrderInvoiceDocuments(string $orderId, array $files): ApiResponse
    {
        return $this->httpClient->postMultipart("/orders/{$orderId}/invoice-documents", $files);
    }
}
