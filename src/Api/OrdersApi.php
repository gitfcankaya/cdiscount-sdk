<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Orders API - Endpoints for order management
 */
class OrdersApi extends BaseApi
{
    /**
     * Get orders count
     *
     * @param array $params
     *   - reference: string
     *   - salesChannelId: string
     *   - businessOrder: bool
     *   - status: string
     *   - supplyMode: string
     *   - shippingCountry: string
     *   - updatedAtMin: string
     *   - updatedAtMax: string
     *   - createdAtMin: string
     *   - createdAtMax: string
     *   - shippedAtMin: string
     *   - shippedAtMax: string
     * @return ApiResponse
     */
    public function getOrdersCount(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/orders/count', $this->buildQueryParams($params));
    }

    /**
     * Get orders
     *
     * @param array $params
     *   - reference: string
     *   - salesChannelId: string
     *   - businessOrder: bool
     *   - status: string (Accepted, InPreparation, Shipped, Delivered, Cancelled)
     *   - supplyMode: string
     *   - shippingCountry: string
     *   - updatedAtMin: string
     *   - updatedAtMax: string
     *   - createdAtMin: string
     *   - createdAtMax: string
     *   - shippedAtMin: string
     *   - shippedAtMax: string
     *   - pageIndex: int
     *   - pageSize: int
     *   - sort: string
     *   - desc: string
     * @return ApiResponse
     */
    public function getOrders(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/orders', $this->buildQueryParams($params));
    }

    /**
     * Get a specific order
     *
     * @param string $orderId
     * @return ApiResponse
     */
    public function getOrder(string $orderId): ApiResponse
    {
        return $this->httpClient->get("/orders/{$orderId}");
    }

    /**
     * Validate/Accept an order
     *
     * @param string $orderId
     * @param string $approvalStatus Approval status: Accepted or Refused
     * @return ApiResponse
     */
    public function validateOrder(string $orderId, string $approvalStatus = 'Accepted'): ApiResponse
    {
        return $this->httpClient->post("/orders/{$orderId}/approval-status", [
            'approval_status' => $approvalStatus,
        ]);
    }

    /**
     * Ship an order
     *
     * @param string $orderId
     * @param array $shipments Array of shipment parcels
     * @return ApiResponse
     */
    public function shipOrder(string $orderId, array $shipments): ApiResponse
    {
        return $this->httpClient->post("/orders/{$orderId}/shipments", $shipments);
    }

    /**
     * Get cancellation reasons
     *
     * @param string|null $salesChannel
     * @param string|null $userType
     * @param string|null $orderStatus
     * @return ApiResponse
     */
    public function getCancellationReasons(
        ?string $salesChannel = null,
        ?string $userType = null,
        ?string $orderStatus = null
    ): ApiResponse {
        $params = $this->buildQueryParams([
            'salesChannel' => $salesChannel,
            'userType' => $userType,
            'orderStatus' => $orderStatus,
        ]);

        return $this->httpClient->get('/cancellation-reasons', $params);
    }

    /**
     * Get order cancellation requests
     *
     * @param array $params
     *   - orderSellerId: string
     *   - cancellationRequestId: string
     *   - lineId: string
     *   - offerId: string
     *   - salesChannel: string
     *   - pageIndex: int
     *   - pageSize: int
     * @return ApiResponse
     */
    public function getCancellationRequests(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/order-cancellation-requests', $this->buildQueryParams($params));
    }

    /**
     * Create order cancellation request (full cancellation)
     *
     * @param string $orderId
     * @param string $reason
     * @param bool $shippingCostRefund
     * @return ApiResponse
     */
    public function createCancellationRequest(
        string $orderId,
        string $reason,
        bool $shippingCostRefund = true
    ): ApiResponse {
        return $this->httpClient->post('/order-cancellation-requests', [
            'orderId' => $orderId,
            'reason' => $reason,
            'shippingCostRefund' => $shippingCostRefund,
        ]);
    }

    /**
     * Create partial cancellation request
     *
     * @param string $orderSellerId
     * @param array $lines Array of lines to cancel with reason
     * @param bool $shippingCostRefund
     * @return ApiResponse
     */
    public function createPartialCancellationRequest(
        string $orderSellerId,
        array $lines,
        bool $shippingCostRefund = true
    ): ApiResponse {
        return $this->httpClient->post('/order-partial-cancellation-requests', [
            'orderSellerId' => $orderSellerId,
            'lines' => $lines,
            'shippingCostRefund' => $shippingCostRefund,
        ]);
    }

    /**
     * Get commercial gesture available amounts
     *
     * @param string $orderId
     * @return ApiResponse
     */
    public function getCommercialGestureAvailableAmounts(string $orderId): ApiResponse
    {
        return $this->httpClient->get("/orders/{$orderId}/commercial-gestures-available-amounts");
    }

    /**
     * Get commercial gesture requests
     *
     * @param array $params
     *   - orderId: string
     *   - status: string
     *   - createdAtMin: string
     *   - createdAtMax: string
     *   - updatedAtMin: string
     *   - updatedAtMax: string
     *   - limit: int
     *   - cursor: string
     * @return ApiResponse
     */
    public function getCommercialGestureRequests(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/order-commercial-gesture-requests', $this->buildQueryParams($params));
    }

    /**
     * Create commercial gesture request
     *
     * @param string $orderId
     * @param string $reason
     * @param float $amount
     * @param string|null $reasonDetails
     * @return ApiResponse
     */
    public function createCommercialGestureRequest(
        string $orderId,
        string $reason,
        float $amount,
        ?string $reasonDetails = null
    ): ApiResponse {
        $data = [
            'orderId' => $orderId,
            'reason' => $reason,
            'amount' => $amount,
        ];

        if ($reasonDetails !== null) {
            $data['reasonDetails'] = $reasonDetails;
        }

        return $this->httpClient->post('/order-commercial-gesture-requests', $data);
    }

    /**
     * Get commercial gesture request by ID
     *
     * @param string $requestId
     * @return ApiResponse
     */
    public function getCommercialGestureRequest(string $requestId): ApiResponse
    {
        return $this->httpClient->get("/order-commercial-gesture-requests/{$requestId}");
    }

    /**
     * Get sales channel commercial gesture configuration
     *
     * @param string $salesChannelId
     * @return ApiResponse
     */
    public function getSalesChannelCommercialGestureConfiguration(string $salesChannelId): ApiResponse
    {
        return $this->httpClient->get("/sales-channel-commercial-gesture-configurations/{$salesChannelId}");
    }
}
