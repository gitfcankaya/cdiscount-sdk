<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Http;

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

    /**
     * HttpClient constructor
     *
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
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
     *
     * @return string
     * @throws AuthenticationException
     */
    public function authenticate(): string
    {
        if ($this->config->isTokenValid()) {
            return $this->config->getAccessToken();
        }

        try {
            $response = $this->authClient->post('/auth/realms/maas/protocol/openid-connect/token', [
                RequestOptions::FORM_PARAMS => [
                    'client_id' => $this->config->getClientId(),
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

            $this->config->setAccessToken(
                $data['access_token'],
                (int) ($data['expires_in'] ?? 7200)
            );

            return $data['access_token'];
        } catch (GuzzleException $e) {
            throw new AuthenticationException(
                'Authentication request failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Make API request
     *
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return ApiResponse
     * @throws ApiException
     * @throws AuthenticationException
     */
    public function request(string $method, string $endpoint, array $options = []): ApiResponse
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
            return $this->handleResponse($response);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
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
}
