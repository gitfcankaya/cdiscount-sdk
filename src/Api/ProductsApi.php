<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Products API - Endpoints for product management
 */
class ProductsApi extends BaseApi
{
    /**
     * Get categories count
     *
     * @param string|null $acceptLanguage Language code (en-US, fr-FR, es-ES)
     * @return ApiResponse
     */
    public function getCategoriesCount(?string $acceptLanguage = null): ApiResponse
    {
        $headers = [];
        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->get('/categories/count', [], $headers);
    }

    /**
     * Get categories
     *
     * @param array $params
     *   - pageIndex: int
     *   - pageSize: int
     *   - sort: string
     *   - desc: string
     *   - fields: string
     * @param string|null $acceptLanguage
     * @return ApiResponse
     */
    public function getCategories(array $params = [], ?string $acceptLanguage = null): ApiResponse
    {
        $headers = [];
        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->get('/categories', $this->buildQueryParams($params), $headers);
    }

    /**
     * Get a specific category
     *
     * @param string $categoryReference
     * @param string|null $acceptLanguage
     * @return ApiResponse
     */
    public function getCategory(string $categoryReference, ?string $acceptLanguage = null): ApiResponse
    {
        $headers = [];
        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->get("/categories/{$categoryReference}", [], $headers);
    }

    /**
     * Get category properties
     *
     * @param string $categoryReference
     * @param string|null $acceptLanguage
     * @return ApiResponse
     */
    public function getCategoryProperties(string $categoryReference, ?string $acceptLanguage = null): ApiResponse
    {
        $headers = [];
        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->get("/categories/{$categoryReference}/properties", [], $headers);
    }

    /**
     * Get brands
     *
     * @param int|null $pageIndex
     * @param int|null $pageSize
     * @param string|null $fields
     * @return ApiResponse
     */
    public function getBrands(?int $pageIndex = null, ?int $pageSize = null, ?string $fields = null): ApiResponse
    {
        $params = $this->buildQueryParams([
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
            'fields' => $fields,
        ]);

        return $this->httpClient->get('/brands', $params);
    }

    /**
     * Get products count
     *
     * @param string|null $categoryReference
     * @param string|null $acceptLanguage
     * @return ApiResponse
     */
    public function getProductsCount(?string $categoryReference = null, ?string $acceptLanguage = null): ApiResponse
    {
        $params = $this->buildQueryParams([
            'categoryReference' => $categoryReference,
        ]);

        $headers = [];
        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->get('/products/count', $params, $headers);
    }

    /**
     * Get products
     *
     * @param array $params
     *   - gtin: string
     *   - categoryReference: string
     *   - cursor: string
     *   - limit: int
     *   - fields: string
     * @param string|null $acceptLanguage
     * @return ApiResponse
     */
    public function getProducts(array $params = [], ?string $acceptLanguage = null): ApiResponse
    {
        $headers = [];
        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->get('/products', $this->buildQueryParams($params), $headers);
    }

    /**
     * Submit products for integration
     *
     * @param array $products Array of products to submit
     * @param string $language Language code (en-US, fr-FR, es-ES)
     * @return ApiResponse
     */
    public function submitProducts(array $products, string $language = 'fr-FR'): ApiResponse
    {
        $headers = ['Accept-Language' => $language];

        return $this->httpClient->post('/products-integration', ['products' => $products], $headers);
    }

    /**
     * Get product integration reports
     *
     * @param array $params
     *   - packageId: string
     *   - gtin: int
     *   - pageIndex: int
     *   - pageSize: int
     *   - fields: string
     *   - sort: string
     *   - desc: string
     * @param string|null $acceptLanguage
     * @return ApiResponse
     */
    public function getProductIntegrationReports(array $params = [], ?string $acceptLanguage = null): ApiResponse
    {
        $headers = [];
        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->get('/products-integration-reports', $this->buildQueryParams($params), $headers);
    }
}
