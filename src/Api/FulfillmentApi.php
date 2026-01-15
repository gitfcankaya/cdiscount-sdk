<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Fulfillment API - Endpoints for Octopia Fulfillment management
 */
class FulfillmentApi extends BaseApi
{
    /**
     * Create fulfillment products
     *
     * @param array $products
     * @return ApiResponse
     */
    public function createFulfillmentProducts(array $products): ApiResponse
    {
        return $this->httpClient->post('/fulfillment-products', $products);
    }

    /**
     * Get inbound shipments count
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getInboundShipmentsCount(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/inbound-shipments/count', $this->buildQueryParams($params));
    }

    /**
     * Get inbound shipments
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getInboundShipments(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/inbound-shipments', $this->buildQueryParams($params));
    }

    /**
     * Create inbound shipment
     *
     * @param array $data
     * @return ApiResponse
     */
    public function createInboundShipment(array $data): ApiResponse
    {
        return $this->httpClient->post('/inbound-shipments', $data);
    }

    /**
     * Get a specific inbound shipment
     *
     * @param string $inboundShipmentId
     * @return ApiResponse
     */
    public function getInboundShipment(string $inboundShipmentId): ApiResponse
    {
        return $this->httpClient->get("/inbound-shipments/{$inboundShipmentId}");
    }

    /**
     * Get inbound shipment delivery notes
     *
     * @param string $inboundShipmentId
     * @return ApiResponse
     */
    public function getInboundShipmentDeliveryNotes(string $inboundShipmentId): ApiResponse
    {
        return $this->httpClient->get("/inbound-shipments/{$inboundShipmentId}/delivery-notes");
    }

    /**
     * Get stocks
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getStocks(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/stocks', $this->buildQueryParams($params));
    }

    /**
     * Get a specific stock
     *
     * @param string $stockId
     * @return ApiResponse
     */
    public function getStock(string $stockId): ApiResponse
    {
        return $this->httpClient->get("/stocks/{$stockId}");
    }

    /**
     * Get stock seller references
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getStockSellerReferences(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/stock-seller-references', $this->buildQueryParams($params));
    }

    /**
     * Get outbound shipments
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getOutboundShipments(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/outbound-shipments', $this->buildQueryParams($params));
    }

    /**
     * Create outbound shipment
     *
     * @param array $data
     * @return ApiResponse
     */
    public function createOutboundShipment(array $data): ApiResponse
    {
        return $this->httpClient->post('/outbound-shipments', $data);
    }

    /**
     * Get a specific outbound shipment
     *
     * @param string $outboundShipmentId
     * @return ApiResponse
     */
    public function getOutboundShipment(string $outboundShipmentId): ApiResponse
    {
        return $this->httpClient->get("/outbound-shipments/{$outboundShipmentId}");
    }

    /**
     * Create outbound cancellation request
     *
     * @param array $data
     * @return ApiResponse
     */
    public function createOutboundCancellationRequest(array $data): ApiResponse
    {
        return $this->httpClient->post('/outbound-cancellation-requests', $data);
    }

    /**
     * Get outbound cancellation request
     *
     * @param string $outboundCancellationRequestId
     * @return ApiResponse
     */
    public function getOutboundCancellationRequest(string $outboundCancellationRequestId): ApiResponse
    {
        return $this->httpClient->get("/outbound-cancellation-requests/{$outboundCancellationRequestId}");
    }

    /**
     * Get returns count
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getReturnsCount(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/returns/count', $this->buildQueryParams($params));
    }

    /**
     * Get returns
     *
     * @param array $params
     * @return ApiResponse
     */
    public function getReturns(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/returns', $this->buildQueryParams($params));
    }

    /**
     * Create return
     *
     * @param array $data
     * @return ApiResponse
     */
    public function createReturn(array $data): ApiResponse
    {
        return $this->httpClient->post('/returns', $data);
    }

    /**
     * Get a specific return
     *
     * @param string $returnId
     * @return ApiResponse
     */
    public function getReturn(string $returnId): ApiResponse
    {
        return $this->httpClient->get("/returns/{$returnId}");
    }

    /**
     * Get return labels
     *
     * @param string $returnId
     * @return ApiResponse
     */
    public function getReturnLabels(string $returnId): ApiResponse
    {
        return $this->httpClient->get("/returns/{$returnId}/labels");
    }
}
