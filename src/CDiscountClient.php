<?php

declare(strict_types=1);

namespace CDiscount\Sdk;

use CDiscount\Sdk\Api\DiscussionsApi;
use CDiscount\Sdk\Api\FinanceApi;
use CDiscount\Sdk\Api\FulfillmentApi;
use CDiscount\Sdk\Api\OffersApi;
use CDiscount\Sdk\Api\OrderInvoicesApi;
use CDiscount\Sdk\Api\OrdersApi;
use CDiscount\Sdk\Api\ProductsApi;
use CDiscount\Sdk\Api\SellerApi;
use CDiscount\Sdk\Cache\TokenCache;
use CDiscount\Sdk\Config\Configuration;
use CDiscount\Sdk\Http\HttpClient;

/**
 * Main CDiscount SDK Client
 * 
 * This is the primary entry point for interacting with the Octopia Seller API.
 * All API endpoints are accessible through dedicated API classes.
 */
class CDiscountClient
{
    /** @var Configuration */
    private $config;

    /** @var HttpClient */
    private $httpClient;

    /** @var SellerApi */
    private $seller;

    /** @var ProductsApi */
    private $products;

    /** @var OffersApi */
    private $offers;

    /** @var OrdersApi */
    private $orders;

    /** @var OrderInvoicesApi */
    private $orderInvoices;

    /** @var DiscussionsApi */
    private $discussions;

    /** @var FulfillmentApi */
    private $fulfillment;

    /** @var FinanceApi */
    private $finance;

    /**
     * CDiscountClient constructor
     *
     * @param Configuration $config
     * @param TokenCache|null $tokenCache Optional token cache instance
     */
    public function __construct(Configuration $config, ?TokenCache $tokenCache = null)
    {
        $this->config = $config;
        $this->httpClient = new HttpClient($config, $tokenCache);
        $this->initializeApis();
    }

    /**
     * Create client from configuration array
     *
     * @param array $config
     *   - client_id: string (required)
     *   - client_secret: string (required)
     *   - grant_type: string (default: client_credentials)
     *   - base_url_token: string (default: https://auth.octopia-io.net)
     *   - base_url: string (default: https://api.octopia-io.net/seller/v2)
     *   - seller_id: string (optional)
     *   - timeout: int (default: 30)
     *   - debug: bool (default: false)
     *   - token_cache_path: string (optional) Path to token cache file
     * @return static
     */
    public static function create(array $config): self
    {
        $tokenCache = null;
        if (isset($config['token_cache_path'])) {
            $tokenCache = new TokenCache($config['token_cache_path']);
        }

        return new self(Configuration::fromArray($config), $tokenCache);
    }

    /**
     * Create client from JSON configuration file
     *
     * @param string $filePath
     * @param string|null $tokenCachePath Optional path to token cache file
     * @return static
     */
    public static function fromConfigFile(string $filePath, ?string $tokenCachePath = null): self
    {
        $tokenCache = $tokenCachePath !== null ? new TokenCache($tokenCachePath) : null;
        return new self(Configuration::fromJsonFile($filePath), $tokenCache);
    }

    /**
     * Initialize API instances
     *
     * @return void
     */
    private function initializeApis(): void
    {
        $this->seller = new SellerApi($this->httpClient);
        $this->products = new ProductsApi($this->httpClient);
        $this->offers = new OffersApi($this->httpClient);
        $this->orders = new OrdersApi($this->httpClient);
        $this->orderInvoices = new OrderInvoicesApi($this->httpClient);
        $this->discussions = new DiscussionsApi($this->httpClient);
        $this->fulfillment = new FulfillmentApi($this->httpClient);
        $this->finance = new FinanceApi($this->httpClient);
    }

    /**
     * Get Seller API
     *
     * @return SellerApi
     */
    public function seller(): SellerApi
    {
        return $this->seller;
    }

    /**
     * Get Products API
     *
     * @return ProductsApi
     */
    public function products(): ProductsApi
    {
        return $this->products;
    }

    /**
     * Get Offers API
     *
     * @return OffersApi
     */
    public function offers(): OffersApi
    {
        return $this->offers;
    }

    /**
     * Get Orders API
     *
     * @return OrdersApi
     */
    public function orders(): OrdersApi
    {
        return $this->orders;
    }

    /**
     * Get Order Invoices API
     *
     * @return OrderInvoicesApi
     */
    public function orderInvoices(): OrderInvoicesApi
    {
        return $this->orderInvoices;
    }

    /**
     * Get Discussions API
     *
     * @return DiscussionsApi
     */
    public function discussions(): DiscussionsApi
    {
        return $this->discussions;
    }

    /**
     * Get Fulfillment API
     *
     * @return FulfillmentApi
     */
    public function fulfillment(): FulfillmentApi
    {
        return $this->fulfillment;
    }

    /**
     * Get Finance API
     *
     * @return FinanceApi
     */
    public function finance(): FinanceApi
    {
        return $this->finance;
    }

    /**
     * Get HTTP client
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
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
     * Set seller ID for all requests
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
     * Authenticate and get access token
     *
     * @param bool $forceRefresh Force a new token request
     * @return string
     */
    public function authenticate(bool $forceRefresh = false): string
    {
        return $this->httpClient->authenticate($forceRefresh);
    }

    /**
     * Refresh the token (force new token request)
     *
     * @return string
     */
    public function refreshToken(): string
    {
        return $this->httpClient->refreshToken();
    }

    /**
     * Clear authentication token (both memory and file cache)
     *
     * @return $this
     */
    public function clearToken(): self
    {
        $this->httpClient->clearAllTokens();
        return $this;
    }

    /**
     * Check if token is valid
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->config->isTokenValid() ||
            $this->httpClient->getTokenCache()->hasValidToken($this->config->getClientId());
    }

    /**
     * Get token information for debugging
     *
     * @return array
     */
    public function getTokenInfo(): array
    {
        return $this->httpClient->getTokenInfo();
    }
}
