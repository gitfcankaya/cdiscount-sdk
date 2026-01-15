<?php

/**
 * Seller API Sample Script
 * 
 * This script demonstrates how to use the Seller API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Seller API Examples ===\n";

    // 1. Get Seller Information
    echo "\n--- Getting Seller Information ---\n";
    $response = $client->seller()->getSeller();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Seller Info');
    }

    // 2. Get Seller Addresses
    echo "\n--- Getting Seller Addresses ---\n";
    $response = $client->seller()->getAddresses();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Seller Addresses');
    }

    // 3. Get Seller Indicators
    echo "\n--- Getting Seller Indicators ---\n";
    $response = $client->seller()->getIndicators();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Seller Indicators');
    }

    // 4. Get Seller Subscriptions
    echo "\n--- Getting Seller Subscriptions ---\n";
    $response = $client->seller()->getSubscriptions();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Seller Subscriptions');
    }

    // 5. Get Carriers
    echo "\n--- Getting Available Carriers ---\n";
    $response = $client->seller()->getCarriers();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Carriers');
    }

    // 6. Get API State
    echo "\n--- Getting API State ---\n";
    $response = $client->seller()->getApiState();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'API State');
    }

    // 7. Get Import Settings
    echo "\n--- Getting Import Settings ---\n";
    $response = $client->seller()->getImportSettings();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Import Settings');
    }

    // 8. Get Parcels Printing Configuration
    echo "\n--- Getting Parcels Printing Configuration ---\n";
    $response = $client->seller()->getParcelsPrintingConfiguration();
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Parcels Printing Configuration');
    }

    echo "\n=== Seller API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
