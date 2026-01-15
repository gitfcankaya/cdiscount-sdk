<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Finance API - Endpoints for financial information
 */
class FinanceApi extends BaseApi
{
    /**
     * Get invoice details
     *
     * @param string $invoiceId
     * @param array $params
     * @return ApiResponse
     */
    public function getInvoiceDetails(string $invoiceId, array $params = []): ApiResponse
    {
        return $this->httpClient->get("/invoices/{$invoiceId}/details", $this->buildQueryParams($params));
    }

    /**
     * Get invoice details count
     *
     * @param string $invoiceId
     * @return ApiResponse
     */
    public function getInvoiceDetailsCount(string $invoiceId): ApiResponse
    {
        return $this->httpClient->get("/invoices/{$invoiceId}/details/count");
    }

    /**
     * Get invoice documents
     *
     * @param string $invoiceId
     * @return ApiResponse
     */
    public function getInvoiceDocuments(string $invoiceId): ApiResponse
    {
        return $this->httpClient->get("/invoices/{$invoiceId}/documents");
    }

    /**
     * Get all invoice documents
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getAllInvoiceDocuments(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/invoice-documents', $this->buildQueryParams($params));
    }

    /**
     * Get operations
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getOperations(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/operations', $this->buildQueryParams($params));
    }

    /**
     * Get operations count
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getOperationsCount(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/operations/count', $this->buildQueryParams($params));
    }

    /**
     * Get payments
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getPayments(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/payments', $this->buildQueryParams($params));
    }

    /**
     * Get payments count
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getPaymentsCount(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/payments/count', $this->buildQueryParams($params));
    }

    /**
     * Get reports (DAC7)
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getReports(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/reports', $this->buildQueryParams($params));
    }

    /**
     * Get report documents
     *
     * @param string $reportId
     * @return ApiResponse
     */
    public function getReportDocuments(string $reportId): ApiResponse
    {
        return $this->httpClient->get("/reports/{$reportId}/report-documents");
    }
}
