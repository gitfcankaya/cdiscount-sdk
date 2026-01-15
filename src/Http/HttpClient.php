<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Http;

use CDiscount\Sdk\Cache\TokenCache;
use CDiscount\Sdk\Config\Configuration;
use CDiscount\Sdk\Exception\ApiException;
use CDiscount\Sdk\Exception\AuthenticationException;
use CDiscount\Sdk\Response\ApiResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Client for API requests
 */
class HttpClient
{
    /** @var Configuration */
    private $config;

    /** @var Client */
    private $client;

    /** @var Client */
    private $authClient;

    /** @var TokenCache */
    private $tokenCache;

    /** @var int Maximum retry attempts for token refresh */
    private const MAX_RETRY_ATTEMPTS = 1;

    /**
     * HttpClient constructor
     *
     * @param Configuration $config
     * @param TokenCache|null $tokenCache
     */
    public function __construct(Configuration $config, ?TokenCache $tokenCache = null)
    {
        $this->config = $config;
        $this->tokenCache = $tokenCache ?? new TokenCache();
        $this->initializeClients();
    }

    /**
     * Initialize Guzzle HTTP clients
     *
     * @return void
     */
    private function initializeClients(): void
    {
        $this->client = new Client([
            'base_uri' => $this->config->getBaseUrl(),
            'timeout' => $this->config->getTimeout(),
            'http_errors' => false,
            'verify' => true,
        ]);

        $this->authClient = new Client([
            'base_uri' => $this->config->getBaseUrlToken(),
            'timeout' => $this->config->getTimeout(),
            'http_errors' => false,
            'verify' => true,
        ]);
    }

    /**
     * Authenticate and get access token
     * First checks file cache, then memory, then requests new token
     *
     * @param bool $forceRefresh Force a new token request
     * @return string
     * @throws AuthenticationException
     */
    public function authenticate(bool $forceRefresh = false): string
    {
        $clientId = $this->config->getClientId();

        // If force refresh, clear cached token
        if ($forceRefresh) {
            $this->tokenCache->delete($clientId);
            $this->config->clearToken();
        }

        // 1. Check memory cache first (fastest)
        if (!$forceRefresh && $this->config->isTokenValid()) {
            return $this->config->getAccessToken();
        }

        // 2. Check file cache (persists across requests)
        if (!$forceRefresh) {
            $cachedToken = $this->tokenCache->getValidToken($clientId);
            if ($cachedToken !== null) {
                // Load into memory cache
                $expiresAt = $this->tokenCache->getExpiresAt($clientId);
                $remainingTime = $expiresAt - time();
                $this->config->setAccessToken($cachedToken, $remainingTime);

                if ($this->config->isDebug()) {
                    $this->logDebug("Token loaded from file cache. Expires at: " . date('Y-m-d H:i:s', $expiresAt));
                }

                return $cachedToken;
            }
        }

        // 3. Request new token from OAuth server
        return $this->requestNewToken();
    }

    /**
     * Request a new token from the OAuth server
     *
     * @return string
     * @throws AuthenticationException
     */
    private function requestNewToken(): string
    {
        $clientId = $this->config->getClientId();

        if ($this->config->isDebug()) {
            $this->logDebug("Requesting new token from OAuth server...");
        }

        try {
            $response = $this->authClient->post('/auth/realms/maas/protocol/openid-connect/token', [
                RequestOptions::FORM_PARAMS => [
                    'client_id' => $clientId,
                    'client_secret' => $this->config->getClientSecret(),
                    'grant_type' => $this->config->getGrantType(),
                ],
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if ($statusCode !== 200) {
                $message = $data['error_description'] ?? $data['error'] ?? 'Authentication failed';
                throw new AuthenticationException($message, $statusCode);
            }

            if (!isset($data['access_token'])) {
                throw new AuthenticationException('No access token in response');
            }

            $accessToken = $data['access_token'];
            $expiresIn = (int) ($data['expires_in'] ?? 7200);

            // Save to memory cache
            $this->config->setAccessToken($accessToken, $expiresIn);

            // Save to file cache
            $this->tokenCache->save($clientId, $accessToken, $expiresIn);

            if ($this->config->isDebug()) {
                $this->logDebug("New token obtained. Expires in: {$expiresIn} seconds");
            }

            return $accessToken;
        } catch (GuzzleException $e) {
            throw new AuthenticationException(
                'Authentication request failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Refresh the token (force new token request)
     *
     * @return string
     * @throws AuthenticationException
     */
    public function refreshToken(): string
    {
        return $this->authenticate(true);
    }

    /**
     * Make API request with automatic token refresh on 401
     *
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @param int $retryCount
     * @return ApiResponse
     * @throws ApiException
     * @throws AuthenticationException
     */
    public function request(string $method, string $endpoint, array $options = [], int $retryCount = 0): ApiResponse
    {
        $token = $this->authenticate();

        $defaultHeaders = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];

        if ($this->config->getSellerId()) {
            $defaultHeaders['SellerId'] = $this->config->getSellerId();
        }

        $options[RequestOptions::HEADERS] = array_merge(
            $defaultHeaders,
            $options[RequestOptions::HEADERS] ?? []
        );

        try {
            $response = $this->client->request($method, $endpoint, $options);
            $statusCode = $response->getStatusCode();

            // Handle 401 Unauthorized - Token might be invalid/expired
            if ($statusCode === 401 && $retryCount < self::MAX_RETRY_ATTEMPTS) {
                if ($this->config->isDebug()) {
                    $this->logDebug("Received 401 Unauthorized. Refreshing token and retrying...");
                }

                // Force refresh token and retry
                $this->refreshToken();
                return $this->request($method, $endpoint, $options, $retryCount + 1);
            }

            return $this->handleResponse($response);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();

                // Handle 401 Unauthorized - Token might be invalid/expired
                if ($statusCode === 401 && $retryCount < self::MAX_RETRY_ATTEMPTS) {
                    if ($this->config->isDebug()) {
                        $this->logDebug("Received 401 Unauthorized. Refreshing token and retrying...");
                    }

                    // Force refresh token and retry
                    $this->refreshToken();
                    return $this->request($method, $endpoint, $options, $retryCount + 1);
                }

                return $this->handleResponse($e->getResponse());
            }
            throw new ApiException(
                'Request failed: ' . $e->getMessage(),
                0,
                null,
                $e
            );
        } catch (GuzzleException $e) {
            throw new ApiException(
                'Request failed: ' . $e->getMessage(),
                0,
                null,
                $e
            );
        }
    }

    /**
     * Handle API response
     *
     * @param ResponseInterface $response
     * @return ApiResponse
     * @throws ApiException
     */
    private function handleResponse(ResponseInterface $response): ApiResponse
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $headers = $response->getHeaders();

        $data = null;
        if (!empty($body)) {
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $data = null;
            }
        }

        $apiResponse = new ApiResponse($statusCode, $data, $headers, $body);

        // Handle error responses
        if ($statusCode >= 400) {
            throw ApiException::fromResponse($statusCode, $body ?: '{}');
        }

        return $apiResponse;
    }

    /**
     * Make GET request
     *
     * @param string $endpoint
     * @param array $query
     * @param array $headers
     * @return ApiResponse
     */
    public function get(string $endpoint, array $query = [], array $headers = []): ApiResponse
    {
        $options = [];

        if (!empty($query)) {
            $options[RequestOptions::QUERY] = $query;
        }

        if (!empty($headers)) {
            $options[RequestOptions::HEADERS] = $headers;
        }

        return $this->request('GET', $endpoint, $options);
    }

    /**
     * Make POST request
     *
     * @param string $endpoint
     * @param array|null $body
     * @param array $headers
     * @return ApiResponse
     */
    public function post(string $endpoint, ?array $body = null, array $headers = []): ApiResponse
    {
        $options = [
            RequestOptions::HEADERS => array_merge([
                'Content-Type' => 'application/json',
            ], $headers),
        ];

        if ($body !== null) {
            $options[RequestOptions::JSON] = $body;
        }

        return $this->request('POST', $endpoint, $options);
    }

    /**
     * Make POST request with form data
     *
     * @param string $endpoint
     * @param array $formData
     * @param array $headers
     * @return ApiResponse
     */
    public function postForm(string $endpoint, array $formData, array $headers = []): ApiResponse
    {
        $options = [
            RequestOptions::HEADERS => array_merge([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ], $headers),
            RequestOptions::FORM_PARAMS => $formData,
        ];

        return $this->request('POST', $endpoint, $options);
    }

    /**
     * Make POST request with multipart form data
     *
     * @param string $endpoint
     * @param array $multipart
     * @param array $headers
     * @return ApiResponse
     */
    public function postMultipart(string $endpoint, array $multipart, array $headers = []): ApiResponse
    {
        $options = [
            RequestOptions::HEADERS => $headers,
            RequestOptions::MULTIPART => $multipart,
        ];

        return $this->request('POST', $endpoint, $options);
    }

    /**
     * Make PUT request
     *
     * @param string $endpoint
     * @param array|null $body
     * @param array $headers
     * @return ApiResponse
     */
    public function put(string $endpoint, ?array $body = null, array $headers = []): ApiResponse
    {
        $options = [
            RequestOptions::HEADERS => array_merge([
                'Content-Type' => 'application/json',
            ], $headers),
        ];

        if ($body !== null) {
            $options[RequestOptions::JSON] = $body;
        }

        return $this->request('PUT', $endpoint, $options);
    }

    /**
     * Make PATCH request
     *
     * @param string $endpoint
     * @param array|null $body
     * @param array $headers
     * @return ApiResponse
     */
    public function patch(string $endpoint, ?array $body = null, array $headers = []): ApiResponse
    {
        $options = [
            RequestOptions::HEADERS => array_merge([
                'Content-Type' => 'application/json',
            ], $headers),
        ];

        if ($body !== null) {
            $options[RequestOptions::JSON] = $body;
        }

        return $this->request('PATCH', $endpoint, $options);
    }

    /**
     * Make DELETE request
     *
     * @param string $endpoint
     * @param array $headers
     * @return ApiResponse
     */
    public function delete(string $endpoint, array $headers = []): ApiResponse
    {
        $options = [];

        if (!empty($headers)) {
            $options[RequestOptions::HEADERS] = $headers;
        }

        return $this->request('DELETE', $endpoint, $options);
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Get token cache
     *
     * @return TokenCache
     */
    public function getTokenCache(): TokenCache
    {
        return $this->tokenCache;
    }

    /**
     * Set seller ID for requests
     *
     * @param string $sellerId
     * @return $this
     */
    public function setSellerId(string $sellerId): self
    {
        $this->config->setSellerId($sellerId);
        return $this;
    }

    /**
     * Log debug message
     *
     * @param string $message
     * @return void
     */
    private function logDebug(string $message): void
    {
        if ($this->config->isDebug()) {
            error_log("[CDiscount SDK] " . date('Y-m-d H:i:s') . " - " . $message);
        }
    }

    /**
     * Get token info for debugging
     *
     * @return array
     */
    public function getTokenInfo(): array
    {
        $clientId = $this->config->getClientId();

        return [
            'has_memory_token' => $this->config->getAccessToken() !== null,
            'memory_token_valid' => $this->config->isTokenValid(),
            'has_file_token' => $this->tokenCache->hasValidToken($clientId),
            'file_token_expires_at' => $this->tokenCache->getExpiresAt($clientId),
            'file_token_expires_at_readable' => $this->tokenCache->getExpiresAt($clientId)
                ? date('Y-m-d H:i:s', $this->tokenCache->getExpiresAt($clientId))
                : null,
            'file_token_remaining_seconds' => $this->tokenCache->getRemainingLifetime($clientId),
            'cache_file_path' => $this->tokenCache->getCacheFilePath(),
        ];
    }

    /**
     * Clear all cached tokens (both memory and file)
     *
     * @return $this
     */
    public function clearAllTokens(): self
    {
        $this->config->clearToken();
        $this->tokenCache->delete($this->config->getClientId());
        return $this;
    }
}
