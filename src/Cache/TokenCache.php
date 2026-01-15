<?php

declare(strict_types=1);

namespace CDiscount\Sdk\Cache;

/**
 * File-based token cache for persisting OAuth tokens across requests
 */
class TokenCache
{
    /** @var string */
    private $cacheFilePath;

    /** @var array|null */
    private $cachedData;

    /**
     * Default cache file name
     */
    public const DEFAULT_CACHE_FILE = '.cdiscount_token_cache.json';

    /**
     * TokenCache constructor
     *
     * @param string|null $cacheFilePath Path to cache file. If null, uses system temp directory
     */
    public function __construct(?string $cacheFilePath = null)
    {
        if ($cacheFilePath === null) {
            $cacheFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::DEFAULT_CACHE_FILE;
        }

        $this->cacheFilePath = $cacheFilePath;
        $this->cachedData = null;
    }

    /**
     * Get cached token data
     *
     * @param string $clientId Client ID to identify the token (supports multiple clients)
     * @return array|null Returns ['access_token' => string, 'expires_at' => int] or null
     */
    public function get(string $clientId): ?array
    {
        $data = $this->loadCache();

        if (!isset($data[$clientId])) {
            return null;
        }

        $tokenData = $data[$clientId];

        // Validate structure
        if (!isset($tokenData['access_token'], $tokenData['expires_at'])) {
            return null;
        }

        return $tokenData;
    }

    /**
     * Get valid token if exists and not expired
     *
     * @param string $clientId Client ID to identify the token
     * @param int $bufferSeconds Seconds before expiry to consider token invalid (default 60)
     * @return string|null Returns access token or null if invalid/expired
     */
    public function getValidToken(string $clientId, int $bufferSeconds = 60): ?string
    {
        $tokenData = $this->get($clientId);

        if ($tokenData === null) {
            return null;
        }

        // Check if token is expired (with buffer)
        $currentTime = time();
        $expiresAt = (int) $tokenData['expires_at'];

        if ($currentTime >= ($expiresAt - $bufferSeconds)) {
            // Token is expired or about to expire
            $this->delete($clientId);
            return null;
        }

        return $tokenData['access_token'];
    }

    /**
     * Save token to cache
     *
     * @param string $clientId Client ID to identify the token
     * @param string $accessToken The access token
     * @param int $expiresIn Token lifetime in seconds
     * @return bool True on success
     */
    public function save(string $clientId, string $accessToken, int $expiresIn): bool
    {
        $data = $this->loadCache();

        $expiresAt = time() + $expiresIn;

        $data[$clientId] = [
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
            'expires_at' => $expiresAt,
            'expires_at_readable' => date('Y-m-d H:i:s', $expiresAt),
            'created_at' => time(),
            'created_at_readable' => date('Y-m-d H:i:s'),
        ];

        return $this->saveCache($data);
    }

    /**
     * Delete token from cache
     *
     * @param string $clientId Client ID to identify the token
     * @return bool True on success
     */
    public function delete(string $clientId): bool
    {
        $data = $this->loadCache();

        if (isset($data[$clientId])) {
            unset($data[$clientId]);
            return $this->saveCache($data);
        }

        return true;
    }

    /**
     * Clear all cached tokens
     *
     * @return bool True on success
     */
    public function clear(): bool
    {
        $this->cachedData = [];

        if (file_exists($this->cacheFilePath)) {
            return @unlink($this->cacheFilePath);
        }

        return true;
    }

    /**
     * Check if a valid token exists in cache
     *
     * @param string $clientId Client ID to identify the token
     * @param int $bufferSeconds Seconds before expiry to consider token invalid
     * @return bool True if valid token exists
     */
    public function hasValidToken(string $clientId, int $bufferSeconds = 60): bool
    {
        return $this->getValidToken($clientId, $bufferSeconds) !== null;
    }

    /**
     * Get token expiration time
     *
     * @param string $clientId Client ID to identify the token
     * @return int|null Unix timestamp when token expires, or null if no token
     */
    public function getExpiresAt(string $clientId): ?int
    {
        $tokenData = $this->get($clientId);

        if ($tokenData === null) {
            return null;
        }

        return (int) $tokenData['expires_at'];
    }

    /**
     * Get remaining token lifetime in seconds
     *
     * @param string $clientId Client ID to identify the token
     * @return int|null Remaining seconds, or null if no token, or negative if expired
     */
    public function getRemainingLifetime(string $clientId): ?int
    {
        $expiresAt = $this->getExpiresAt($clientId);

        if ($expiresAt === null) {
            return null;
        }

        return $expiresAt - time();
    }

    /**
     * Get cache file path
     *
     * @return string
     */
    public function getCacheFilePath(): string
    {
        return $this->cacheFilePath;
    }

    /**
     * Load cache data from file
     *
     * @return array
     */
    private function loadCache(): array
    {
        // Return cached data if already loaded
        if ($this->cachedData !== null) {
            return $this->cachedData;
        }

        if (!file_exists($this->cacheFilePath)) {
            $this->cachedData = [];
            return $this->cachedData;
        }

        $content = @file_get_contents($this->cacheFilePath);

        if ($content === false) {
            $this->cachedData = [];
            return $this->cachedData;
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->cachedData = [];
            return $this->cachedData;
        }

        $this->cachedData = $data;
        return $this->cachedData;
    }

    /**
     * Save cache data to file
     *
     * @param array $data
     * @return bool
     */
    private function saveCache(array $data): bool
    {
        $this->cachedData = $data;

        $directory = dirname($this->cacheFilePath);

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0755, true)) {
                return false;
            }
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            return false;
        }

        $result = @file_put_contents($this->cacheFilePath, $json, LOCK_EX);

        return $result !== false;
    }
}
