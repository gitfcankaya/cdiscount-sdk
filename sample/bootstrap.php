<?php

/**
 * Bootstrap file for SDK samples
 * 
 * This file initializes the SDK client for all sample scripts.
 * Copy config.example.json to config.json and update with your credentials.
 */

declare(strict_types=1);

// Autoload
require_once __DIR__ . '/../vendor/autoload.php';

use CDiscount\Sdk\CDiscountClient;
use CDiscount\Sdk\Exception\ApiException;
use CDiscount\Sdk\Exception\AuthenticationException;
use CDiscount\Sdk\Exception\ValidationException;

/**
 * Create and return a configured SDK client
 *
 * @return CDiscountClient
 */
function createClient(): CDiscountClient
{
    $configPath = __DIR__ . '/../config.json';

    if (!file_exists($configPath)) {
        die("Error: config.json not found. Please copy config.example.json to config.json and update with your credentials.\n");
    }

    return CDiscountClient::fromConfigFile($configPath);
}

/**
 * Print response data in a formatted way
 *
 * @param mixed $data
 * @param string $title
 * @return void
 */
function printResponse($data, string $title = 'Response'): void
{
    echo "\n=== {$title} ===\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n";
}

/**
 * Handle exceptions in a consistent way
 *
 * @param \Exception $e
 * @return void
 */
function handleException(\Exception $e): void
{
    if ($e instanceof AuthenticationException) {
        echo "Authentication Error: " . $e->getMessage() . "\n";
    } elseif ($e instanceof ApiException) {
        echo "API Error: " . $e->getMessage() . "\n";
        echo "Status Code: " . $e->getStatusCode() . "\n";
        if ($e->getTraceId()) {
            echo "Trace ID: " . $e->getTraceId() . "\n";
        }
        if ($e->getDetails()) {
            echo "Details: " . json_encode($e->getDetails(), JSON_PRETTY_PRINT) . "\n";
        }
    } elseif ($e instanceof ValidationException) {
        echo "Validation Error: " . $e->getMessage() . "\n";
        if ($e->getErrors()) {
            echo "Errors: " . json_encode($e->getErrors(), JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
