<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Seller API - Endpoints for seller configuration and information
 */
class SellerApi extends BaseApi
{
    /**
     * Get seller information
     *
     * @return ApiResponse
     */
    public function getSeller(): ApiResponse
    {
        return $this->httpClient->get('/sellers');
    }

    /**
     * Get seller addresses
     *
     * @return ApiResponse
     */
    public function getAddresses(): ApiResponse
    {
        return $this->httpClient->get('/sellers/addresses');
    }

    /**
     * Get seller indicators
     *
     * @return ApiResponse
     */
    public function getIndicators(): ApiResponse
    {
        return $this->httpClient->get('/sellers/indicators');
    }

    /**
     * Get seller subscriptions
     *
     * @return ApiResponse
     */
    public function getSubscriptions(): ApiResponse
    {
        return $this->httpClient->get('/sellers/subscriptions');
    }

    /**
     * Get seller delivery modes
     *
     * @deprecated This endpoint is deprecated
     * @return ApiResponse
     */
    public function getDeliveryModes(): ApiResponse
    {
        return $this->httpClient->get('/sellers/delivery-modes');
    }

    /**
     * Get carrier list
     *
     * @return ApiResponse
     */
    public function getCarriers(): ApiResponse
    {
        return $this->httpClient->get('/carriers');
    }
}
