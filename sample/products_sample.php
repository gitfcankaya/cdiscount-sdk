<?php

/**
 * Products API Sample Script
 * 
 * This script demonstrates how to use the Products API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Products API Examples ===\n";

    $locale = 'fr-FR';

    // 1. Get Categories Count
    echo "\n--- Getting Categories Count ---\n";
    $response = $client->products()->getCategoriesCount($locale);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Categories Count');
    }

    // 2. Get Categories (paginated)
    echo "\n--- Getting Categories (First Page) ---\n";
    $response = $client->products()->getCategories([
        'pageIndex' => 1,
        'pageSize' => 10,
        'fields' => 'label,level,isActive,childCount',
    ], $locale);
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Categories');
        echo "Items per page: " . $response->getItemsPerPage() . "\n";
        echo "Has next page: " . ($response->hasNextPage() ? 'Yes' : 'No') . "\n";
    }

    // 3. Get Specific Category
    echo "\n--- Getting Specific Category ---\n";
    $categoryCode = '010101'; // Example category code
    $response = $client->products()->getCategory($categoryCode, $locale);
    if ($response->isSuccess()) {
        printResponse($response->getData(), "Category {$categoryCode}");
    }

    // 4. Get Category Properties
    echo "\n--- Getting Category Properties ---\n";
    $response = $client->products()->getCategoryProperties($categoryCode, $locale);
    if ($response->isSuccess()) {
        printResponse($response->getData(), "Category {$categoryCode} Properties");
    }

    // 5. Get Brands
    echo "\n--- Getting Brands (First 10) ---\n";
    $response = $client->products()->getBrands(1, 10, 'name');
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Brands');
    }

    // 6. Get Products
    echo "\n--- Getting Products ---\n";
    $response = $client->products()->getProducts([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Products');
    }

    // 7. Get Product Integration Reports
    echo "\n--- Getting Product Integration Reports ---\n";
    $response = $client->products()->getProductIntegrationReports([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Product Integration Reports');
    }

    // 8. Submit Products Example (commented - requires valid data)
    /*
    echo "\n--- Submitting Products ---\n";
    $products = [
        [
            'gtin' => '1234567890128',
            'sellerProductReference' => 'AGRA39401',
            'title' => 'My Product Title',
            'description' => 'My product description',
            'shortDescription' => 'Short description',
            'brand' => 'My Brand',
            'categoryCode' => '010201',
            'images' => [
                ['url' => 'https://example.com/image1.jpg', 'isMain' => true],
            ],
        ],
    ];
    $response = $client->products()->submitProducts($products, $locale);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Product Submission Result');
    }
    */

    echo "\n=== Products API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
