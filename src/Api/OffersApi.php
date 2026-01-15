<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Offers API - Endpoints for offer management
 */
class OffersApi extends BaseApi
{
    /**
     * Create an offer package
     *
     * @param string $packageType Package type: Upsert, Update, or Delete
     * @param string $salesChannelId Sales channel identifier
     * @param string|null $acceptLanguage Language code
     * @return ApiResponse
     */
    public function createPackage(
        string $packageType,
        string $salesChannelId,
        ?string $acceptLanguage = 'en-US'
    ): ApiResponse {
        $headers = [
            'salesChannelId' => $salesChannelId,
        ];

        if ($acceptLanguage) {
            $headers['Accept-Language'] = $acceptLanguage;
        }

        return $this->httpClient->post('/offer-packages', ['packageType' => $packageType], $headers);
    }

    /**
     * Get offer packages
     *
     * @param array $params
     *   - state: string (WaitingForCompletion, Ready, IntegrationPending, Integrated, Rejected)
     *   - salesChannelId: string
     *   - limit: int
     * @return ApiResponse
     */
    public function getPackages(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/offer-packages', $this->buildQueryParams($params));
    }

    /**
     * Get a specific offer package
     *
     * @param string $packageId
     * @return ApiResponse
     */
    public function getPackage(string $packageId): ApiResponse
    {
        return $this->httpClient->get("/offer-packages/{$packageId}");
    }

    /**
     * Submit offer package (set to Ready status)
     *
     * @param string $packageId
     * @return ApiResponse
     */
    public function submitPackage(string $packageId): ApiResponse
    {
        return $this->httpClient->patch("/offer-packages/{$packageId}", ['state' => 'Ready']);
    }

    /**
     * Upload offer requests to a package
     *
     * @param string $packageId
     * @param array $offerRequests
     * @return ApiResponse
     */
    public function uploadOfferRequests(string $packageId, array $offerRequests): ApiResponse
    {
        return $this->httpClient->post("/offer-packages/{$packageId}/offer-requests", $offerRequests);
    }

    /**
     * Get offer request results
     *
     * @param string $packageId
     * @param int|null $limit
     * @return ApiResponse
     */
    public function getOfferRequestResults(string $packageId, ?int $limit = null): ApiResponse
    {
        $params = $this->buildQueryParams(['limit' => $limit]);

        return $this->httpClient->get("/offer-packages/{$packageId}/offer-requests-results", $params);
    }

    /**
     * Get offer integration package logs (XML integration)
     *
     * @param int $packageId
     * @param int|null $limit
     * @param int|null $page
     * @return ApiResponse
     */
    public function getOfferIntegrationPackage(int $packageId, ?int $limit = null, ?int $page = null): ApiResponse
    {
        $params = $this->buildQueryParams([
            '$limit' => $limit,
            '$page' => $page,
        ]);

        return $this->httpClient->get("/offer-integration-packages/{$packageId}", $params);
    }

    /**
     * Submit offer package (XML integration)
     *
     * @param string $packageUrl URL to the package zip file
     * @return ApiResponse
     */
    public function submitOfferIntegrationPackage(string $packageUrl): ApiResponse
    {
        return $this->httpClient->post('/offer-integration-packages', null, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Search offers (deprecated)
     *
     * @deprecated Use getOffers instead
     * @param array $searchRequest
     * @param int|null $limit
     * @param int|null $page
     * @return ApiResponse
     */
    public function searchOffers(array $searchRequest, ?int $limit = null, ?int $page = null): ApiResponse
    {
        $params = $this->buildQueryParams([
            '$limit' => $limit,
            '$page' => $page,
        ]);

        return $this->httpClient->post('/offers/search?' . http_build_query($params), $searchRequest);
    }

    /**
     * Get offers
     *
     * @param string $salesChannelId Sales channel identifier (required)
     * @param array $params
     *   - limit: int
     *   - fields: string
     *   - expand: string (salesChannelFeedback)
     *   - offerIds: string
     *   - offerStates: string
     *   - gtins: string
     *   - sellerExternalReferences: string
     *   - updatedAtMin: string
     * @return ApiResponse
     */
    public function getOffers(string $salesChannelId, array $params = []): ApiResponse
    {
        $params['salesChannelId'] = $salesChannelId;

        return $this->httpClient->get('/offers', $this->buildQueryParams($params));
    }

    /**
     * Get competing offer changes (deprecated)
     *
     * @deprecated
     * @return ApiResponse
     */
    public function getCompetingOfferChanges(): ApiResponse
    {
        return $this->httpClient->get('/competing-offer-changes');
    }

    /**
     * Get competing offers (deprecated)
     *
     * @deprecated
     * @param array $products Product IDs
     * @return ApiResponse
     */
    public function getCompetingOffers(array $products): ApiResponse
    {
        $params = [];
        foreach ($products as $product) {
            $params['products'][] = $product;
        }

        return $this->httpClient->get('/competing-offers', $params);
    }
}
