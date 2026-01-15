<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Config;

/**
 * SDK Configuration class for Octopia API credentials and settings
 */
class Configuration
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $grantType;

    /** @var string */
    private $baseUrlToken;

    /** @var string */
    private $baseUrl;

    /** @var string|null */
    private $sellerId;

    /** @var int */
    private $timeout;

    /** @var bool */
    private $debug;

    /** @var string|null */
    private $accessToken;

    /** @var int|null */
    private $tokenExpiresAt;

    /**
     * Default API base URL
     */
    public const DEFAULT_BASE_URL = 'https://api.octopia-io.net/seller/v2';

    /**
     * Default token base URL
     */
    public const DEFAULT_TOKEN_URL = 'https://auth.octopia-io.net';

    /**
     * Default grant type
     */
    public const DEFAULT_GRANT_TYPE = 'client_credentials';

    /**
     * Configuration constructor
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $grantType
     * @param string $baseUrlToken
     * @param string $baseUrl
     * @param string|null $sellerId
     * @param int $timeout
     * @param bool $debug
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $grantType = self::DEFAULT_GRANT_TYPE,
        string $baseUrlToken = self::DEFAULT_TOKEN_URL,
        string $baseUrl = self::DEFAULT_BASE_URL,
        ?string $sellerId = null,
        int $timeout = 30,
        bool $debug = false
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->grantType = $grantType;
        $this->baseUrlToken = rtrim($baseUrlToken, '/');
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->sellerId = $sellerId;
        $this->timeout = $timeout;
        $this->debug = $debug;
    }

    /**
     * Create configuration from array
     *
     * @param array $config
     * @return static
     */
    public static function fromArray(array $config): self
    {
        return new self(
            $config['client_id'] ?? '',
            $config['client_secret'] ?? '',
            $config['grant_type'] ?? self::DEFAULT_GRANT_TYPE,
            $config['base_url_token'] ?? self::DEFAULT_TOKEN_URL,
            $config['base_url'] ?? self::DEFAULT_BASE_URL,
            $config['seller_id'] ?? null,
            $config['timeout'] ?? 30,
            $config['debug'] ?? false
        );
    }

    /**
     * Create configuration from JSON file
     *
     * @param string $filePath
     * @return static
     * @throws \RuntimeException
     */
    public static function fromJsonFile(string $filePath): self
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Configuration file not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        $config = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in configuration file: " . json_last_error_msg());
        }

        return self::fromArray($config);
    }

    /**
     * Get client ID
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Get client secret
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Get grant type
     *
     * @return string
     */
    public function getGrantType(): string
    {
        return $this->grantType;
    }

    /**
     * Get token base URL
     *
     * @return string
     */
    public function getBaseUrlToken(): string
    {
        return $this->baseUrlToken;
    }

    /**
     * Get API base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get seller ID
     *
     * @return string|null
     */
    public function getSellerId(): ?string
    {
        return $this->sellerId;
    }

    /**
     * Set seller ID
     *
     * @param string $sellerId
     * @return $this
     */
    public function setSellerId(string $sellerId): self
    {
        $this->sellerId = $sellerId;
        return $this;
    }

    /**
     * Get request timeout
     *
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Check if debug mode is enabled
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * Get token endpoint URL
     *
     * @return string
     */
    public function getTokenEndpoint(): string
    {
        return $this->baseUrlToken . '/auth/realms/maas/protocol/openid-connect/token';
    }

    /**
     * Set access token
     *
     * @param string $token
     * @param int $expiresIn
     * @return $this
     */
    public function setAccessToken(string $token, int $expiresIn): self
    {
        $this->accessToken = $token;
        $this->tokenExpiresAt = time() + $expiresIn - 60; // 60 seconds buffer
        return $this;
    }

    /**
     * Get access token
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Check if token is valid
     *
     * @return bool
     */
    public function isTokenValid(): bool
    {
        if ($this->accessToken === null || $this->tokenExpiresAt === null) {
            return false;
        }

        return time() < $this->tokenExpiresAt;
    }

    /**
     * Clear access token
     *
     * @return $this
     */
    public function clearToken(): self
    {
        $this->accessToken = null;
        $this->tokenExpiresAt = null;
        return $this;
    }
}
