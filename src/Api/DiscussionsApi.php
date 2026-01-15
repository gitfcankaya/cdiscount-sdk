<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Api;

use CDiscount\Sdk\Response\ApiResponse;

/**
 * Discussions API - Endpoints for discussion/messaging management
 */
class DiscussionsApi extends BaseApi
{
    /**
     * Get sales channel configuration for discussions
     *
     * @param string $salesChannelId
     * @return ApiResponse
     */
    public function getSalesChannelConfiguration(string $salesChannelId): ApiResponse
    {
        return $this->httpClient->get("/sales-channel-configurations/{$salesChannelId}");
    }

    /**
     * Get discussion typologies
     *
     * @param string $salesChannel
     * @param string $orderStatus
     * @param string $typologyCode
     * @param string|null $userType
     * @return ApiResponse
     */
    public function getTypologies(
        string $salesChannel,
        string $orderStatus,
        string $typologyCode = 'order',
        ?string $userType = null
    ): ApiResponse {
        $params = $this->buildQueryParams([
            'salesChannel' => $salesChannel,
            'orderStatus' => $orderStatus,
            'typologyCode' => $typologyCode,
            'userType' => $userType,
        ]);

        return $this->httpClient->get('/typologies', $params);
    }

    /**
     * Count discussions
     *
     * @param string|null $salesChannel
     * @param string|null $graduationCode
     * @param string|null $processStatus
     * @return ApiResponse
     */
    public function getDiscussionsCount(
        ?string $salesChannel = null,
        ?string $graduationCode = null,
        ?string $processStatus = null
    ): ApiResponse {
        $params = $this->buildQueryParams([
            'salesChannel' => $salesChannel,
            'graduationCode' => $graduationCode,
            'processStatus' => $processStatus,
        ]);

        return $this->httpClient->get('/discussions/count', $params);
    }

    /**
     * Get discussions
     *
     * @param array $params
     *   - discussionId: string
     *   - orderSellerId: string
     *   - salesChannel: string
     *   - isOpen: bool
     *   - processStatus: string (ToTreat, Treated)
     *   - typologyCode: string
     *   - subTypologyCode: string
     *   - updatedAtMin: string
     *   - updatedAtMax: string
     *   - pageSize: int
     *   - pageIndex: int
     *   - sort: string
     *   - includeMessages: string (FirstMessage, LastMessage)
     * @return ApiResponse
     */
    public function getDiscussions(array $params = []): ApiResponse
    {
        return $this->httpClient->get('/discussions', $this->buildQueryParams($params));
    }

    /**
     * Create a new discussion
     *
     * @param array $data
     *   - subject: string
     *   - orderId: string
     *   - salesChannel: string
     *   - subtypologyCode: string
     *   - productId: string (optional)
     *   - message: array
     *     - body: string
     *     - receiver: string
     *     - attachments: array (optional)
     * @return ApiResponse
     */
    public function createDiscussion(array $data): ApiResponse
    {
        return $this->httpClient->post('/discussions', $data);
    }

    /**
     * Get a full discussion by ID
     *
     * @param string $discussionId
     * @return ApiResponse
     */
    public function getDiscussion(string $discussionId): ApiResponse
    {
        return $this->httpClient->get("/discussions/{$discussionId}");
    }

    /**
     * Update discussion (open/close)
     *
     * @param string $discussionId
     * @param bool $isOpen
     * @return ApiResponse
     */
    public function updateDiscussion(string $discussionId, bool $isOpen): ApiResponse
    {
        return $this->httpClient->patch("/discussions/{$discussionId}", [
            [
                'opt' => 'replace',
                'path' => '/isOpen',
                'value' => $isOpen ? 'true' : 'false',
            ],
        ]);
    }

    /**
     * Close a discussion
     *
     * @param string $discussionId
     * @return ApiResponse
     */
    public function closeDiscussion(string $discussionId): ApiResponse
    {
        return $this->updateDiscussion($discussionId, false);
    }

    /**
     * Reopen a discussion
     *
     * @param string $discussionId
     * @return ApiResponse
     */
    public function reopenDiscussion(string $discussionId): ApiResponse
    {
        return $this->updateDiscussion($discussionId, true);
    }

    /**
     * Send a message in a discussion
     *
     * @param string $discussionId
     * @param string $body
     * @param string $receiver
     * @param array|null $attachments
     * @return ApiResponse
     */
    public function sendMessage(
        string $discussionId,
        string $body,
        string $receiver,
        ?array $attachments = null
    ): ApiResponse {
        $data = [
            'discussionId' => $discussionId,
            'body' => $body,
            'receiver' => $receiver,
        ];

        if ($attachments !== null) {
            $data['attachments'] = $attachments;
        }

        return $this->httpClient->post('/messages', $data);
    }

    /**
     * Mark message as read
     *
     * @param string $messageId
     * @return ApiResponse
     */
    public function markMessageAsRead(string $messageId): ApiResponse
    {
        return $this->httpClient->patch("/messages/{$messageId}", [
            [
                'opt' => 'replace',
                'path' => '/hasRead',
                'value' => 'true',
            ],
        ]);
    }

    /**
     * Get attachments
     *
     * @return ApiResponse
     */
    public function getAttachments(): ApiResponse
    {
        return $this->httpClient->get('/attachments');
    }
}
