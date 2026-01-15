<?php

/**
 * Offers API Sample Script
 * 
 * This script demonstrates how to use the Offers API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Offers API Examples ===\n";

    $salesChannelId = 'CDISFR';
    $acceptLanguage = 'en-US';

    // 1. Get Offer Packages
    echo "\n--- Getting Offer Packages ---\n";
    $response = $client->offers()->getPackages([
        'salesChannelId' => $salesChannelId,
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Offer Packages');
    }

    // 2. Get Offers
    echo "\n--- Getting Offers ---\n";
    $response = $client->offers()->getOffers($salesChannelId, [
        'limit' => 10,
        'fields' => 'condition,product,integrationPrice,quantity',
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Offers');
    }

    // 3. Get Offers Count
    echo "\n--- Getting Offers Count ---\n";
    $response = $client->offers()->getOffersCount($salesChannelId);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Offers Count');
    }

    // 4. Create Offer Package Example
    echo "\n--- Creating Offer Package (Example) ---\n";
    $response = $client->offers()->createPackage('Upsert', $salesChannelId, $acceptLanguage);
    if ($response->isSuccess()) {
        $packageLocation = $response->getHeader('Content-Location');
        printResponse([
            'message' => 'Package created',
            'location' => $packageLocation,
        ], 'Package Created');

        // Extract package ID from location
        $packageId = basename($packageLocation ?? '');

        if ($packageId) {
            // 5. Upload Offer Requests to Package
            echo "\n--- Uploading Offer Requests ---\n";
            $offerRequests = [
                [
                    'sellerExternalReference' => 'TEST-SKU-001',
                    'product' => [
                        'gtin' => '1234567890000',
                        'reference' => 'AUC1234567890000',
                    ],
                    'condition' => 'New',
                    'price' => [
                        'price' => 49.99,
                        'originPrice' => 59.99,
                    ],
                    'quantity' => 100,
                    'preparationTime' => 2,
                ],
            ];

            $response = $client->offers()->uploadOfferRequests($packageId, $offerRequests);
            if ($response->isSuccess()) {
                printResponse($response->getData(), 'Offer Requests Uploaded');
            }

            // 6. Get Package Details
            echo "\n--- Getting Package Details ---\n";
            $response = $client->offers()->getPackage($packageId);
            if ($response->isSuccess()) {
                printResponse($response->getData(), 'Package Details');
            }

            // 7. Get Offer Requests from Package
            echo "\n--- Getting Offer Requests from Package ---\n";
            $response = $client->offers()->getOfferRequests($packageId);
            if ($response->isSuccess()) {
                printResponse($response->getItems(), 'Offer Requests');
            }

            // 8. Submit Package (commented - will actually process the package)
            /*
            echo "\n--- Submitting Package ---\n";
            $response = $client->offers()->submitPackage($packageId);
            if ($response->isSuccess()) {
                printResponse($response->getData(), 'Package Submitted');
            }
            */

            // 9. Delete Package (cleanup)
            echo "\n--- Deleting Package ---\n";
            $response = $client->offers()->deletePackage($packageId);
            if ($response->isSuccess()) {
                echo "Package deleted successfully.\n";
            }
        }
    }

    // 10. Get Specific Offer (if you have an offer ID)
    /*
    echo "\n--- Getting Specific Offer ---\n";
    $offerId = 'your-offer-id';
    $response = $client->offers()->getOffer($offerId, $salesChannelId);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Offer Details');
    }
    */

    echo "\n=== Offers API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
