<?php

/**
 * Token Cache Sample Script
 * 
 * This script demonstrates how token caching works in the SDK.
 * Tokens are persisted to a JSON file and reused across requests.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use CDiscount\Sdk\CDiscountClient;
use CDiscount\Sdk\Cache\TokenCache;

try {
    echo "=== Token Cache Examples ===\n";

    // Create client - tokens will be cached automatically
    $client = createClient();

    // Get token info before authentication
    echo "\n--- Token Info Before Authentication ---\n";
    $tokenInfo = $client->getTokenInfo();
    printResponse($tokenInfo, 'Token Info (Before)');

    // Authenticate - this will:
    // 1. Check memory cache (empty on first run)
    // 2. Check file cache (may have valid token from previous run)
    // 3. Request new token if none found
    echo "\n--- Authenticating ---\n";
    $token = $client->authenticate();
    echo "✓ Token obtained (first 50 chars): " . substr($token, 0, 50) . "...\n";

    // Get token info after authentication
    echo "\n--- Token Info After Authentication ---\n";
    $tokenInfo = $client->getTokenInfo();
    printResponse($tokenInfo, 'Token Info (After)');

    // Make an API call - uses cached token
    echo "\n--- Making API Call (Uses Cached Token) ---\n";
    $response = $client->seller()->getSeller();
    if ($response->isSuccess()) {
        echo "✓ API call successful using cached token\n";
    }

    // Simulate multiple requests - all use same token
    echo "\n--- Multiple Requests (Same Token) ---\n";
    for ($i = 1; $i <= 3; $i++) {
        $response = $client->seller()->getSeller();
        echo "Request {$i}: " . ($response->isSuccess() ? '✓ Success' : '✗ Failed') . "\n";
    }

    // Show cache file location
    echo "\n--- Cache File Location ---\n";
    $cacheFilePath = $tokenInfo['cache_file_path'];
    echo "Cache file: {$cacheFilePath}\n";

    if (file_exists($cacheFilePath)) {
        echo "Cache file exists: Yes\n";
        echo "Cache file contents:\n";
        echo file_get_contents($cacheFilePath) . "\n";
    }

    // Demonstrate token refresh
    echo "\n--- Force Token Refresh ---\n";
    $newToken = $client->refreshToken();
    echo "✓ New token obtained (first 50 chars): " . substr($newToken, 0, 50) . "...\n";

    $tokenInfo = $client->getTokenInfo();
    echo "New expiration: " . $tokenInfo['file_token_expires_at_readable'] . "\n";
    echo "Remaining seconds: " . $tokenInfo['file_token_remaining_seconds'] . "\n";

    // Clear all tokens example
    /*
    echo "\n--- Clearing All Tokens ---\n";
    $client->clearToken();
    echo "✓ All tokens cleared (memory and file)\n";
    */

    echo "\n=== Token Cache Examples Completed ===\n";

    echo "\n--- How Token Caching Works ---\n";
    echo "1. First request: SDK gets new token from OAuth server\n";
    echo "2. Token is saved to JSON file with expiration time\n";
    echo "3. Subsequent requests (even new PHP processes) reuse cached token\n";
    echo "4. When token expires (or 60 seconds before), new token is obtained\n";
    echo "5. If API returns 401, token is automatically refreshed and request retried\n";

} catch (\Exception $e) {
    handleException($e);
}
