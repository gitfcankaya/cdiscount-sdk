<?php

/**
 * Complete Workflow Sample Script
 * 
 * This script demonstrates a complete e-commerce workflow using the SDK:
 * 1. Get seller information
 * 2. Browse categories and products
 * 3. Create and manage offers
 * 4. Process orders
 * 5. Handle discussions
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "========================================\n";
    echo "  CDiscount SDK Complete Workflow Demo\n";
    echo "========================================\n";

    $salesChannelId = 'CDISFR';
    $locale = 'fr-FR';

    // =====================
    // STEP 1: SELLER INFO
    // =====================
    echo "\n[STEP 1] Getting Seller Information...\n";

    $response = $client->seller()->getSeller();
    if ($response->isSuccess()) {
        $seller = $response->getData();
        echo "✓ Seller: " . ($seller['name'] ?? 'Unknown') . "\n";
        echo "✓ ID: " . ($seller['id'] ?? 'Unknown') . "\n";
    }

    // =====================
    // STEP 2: BROWSE CATALOG
    // =====================
    echo "\n[STEP 2] Browsing Product Catalog...\n";

    // Get categories
    $response = $client->products()->getCategories([
        'pageIndex' => 1,
        'pageSize' => 5,
        'fields' => 'label,level',
    ], $locale);

    if ($response->isSuccess()) {
        $categories = $response->getItems();
        echo "✓ Found " . count($categories) . " categories\n";
        foreach ($categories as $category) {
            echo "  - " . ($category['label'] ?? $category['code'] ?? 'Unknown') . "\n";
        }
    }

    // Get brands
    $response = $client->products()->getBrands(1, 5, 'name');
    if ($response->isSuccess()) {
        $brands = $response->getItems();
        echo "✓ Found " . count($brands) . " brands\n";
    }

    // =====================
    // STEP 3: CHECK OFFERS
    // =====================
    echo "\n[STEP 3] Checking Current Offers...\n";

    $response = $client->offers()->getOffersCount($salesChannelId);
    if ($response->isSuccess()) {
        $count = $response->getData();
        echo "✓ Total offers: " . ($count['count'] ?? $count ?? 'Unknown') . "\n";
    }

    $response = $client->offers()->getOffers($salesChannelId, [
        'limit' => 5,
        'fields' => 'condition,product,integrationPrice,quantity',
    ]);
    if ($response->isSuccess()) {
        $offers = $response->getItems();
        echo "✓ Sample offers:\n";
        foreach ($offers as $offer) {
            $sku = $offer['sellerExternalReference'] ?? $offer['sku'] ?? 'Unknown';
            $price = $offer['integrationPrice']['price'] ?? $offer['price'] ?? 'N/A';
            $qty = $offer['quantity'] ?? 'N/A';
            echo "  - SKU: {$sku}, Price: {$price}, Qty: {$qty}\n";
        }
    }

    // =====================
    // STEP 4: PROCESS ORDERS
    // =====================
    echo "\n[STEP 4] Processing Orders...\n";

    // Get order counts by status
    $statuses = ['WaitingForSellerAcceptation', 'Accepted', 'InPreparation', 'Shipped'];
    foreach ($statuses as $status) {
        $response = $client->orders()->getOrdersCount([
            'salesChannelId' => $salesChannelId,
            'status' => $status,
        ]);
        if ($response->isSuccess()) {
            $count = $response->getData();
            echo "✓ {$status}: " . ($count['count'] ?? $count ?? 0) . " orders\n";
        }
    }

    // Get recent orders
    $response = $client->orders()->getOrders([
        'salesChannelId' => $salesChannelId,
        'pageIndex' => 1,
        'pageSize' => 5,
        'sort' => 'createdAt',
        'order' => 'desc',
    ]);
    if ($response->isSuccess()) {
        $orders = $response->getItems();
        echo "✓ Recent orders:\n";
        foreach ($orders as $order) {
            $orderId = $order['id'] ?? $order['orderId'] ?? 'Unknown';
            $status = $order['status'] ?? 'Unknown';
            $total = $order['totalAmount'] ?? $order['total'] ?? 'N/A';
            echo "  - Order: {$orderId}, Status: {$status}, Total: {$total}\n";
        }
    }

    // =====================
    // STEP 5: CHECK DISCUSSIONS
    // =====================
    echo "\n[STEP 5] Checking Customer Discussions...\n";

    $response = $client->discussions()->getDiscussionsCount([
        'salesChannel' => $salesChannelId,
        'isOpen' => true,
    ]);
    if ($response->isSuccess()) {
        $count = $response->getData();
        echo "✓ Open discussions: " . ($count['count'] ?? $count ?? 0) . "\n";
    }

    $response = $client->discussions()->getDiscussions([
        'salesChannel' => $salesChannelId,
        'isOpen' => true,
        'pageSize' => 5,
    ]);
    if ($response->isSuccess()) {
        $discussions = $response->getItems();
        if (!empty($discussions)) {
            echo "✓ Recent open discussions:\n";
            foreach ($discussions as $discussion) {
                $id = $discussion['id'] ?? 'Unknown';
                $subject = $discussion['subject'] ?? 'No subject';
                echo "  - [{$id}] {$subject}\n";
            }
        }
    }

    // =====================
    // STEP 6: FINANCE SUMMARY
    // =====================
    echo "\n[STEP 6] Finance Summary...\n";

    $response = $client->finance()->getPayments([
        'limit' => 5,
    ]);
    if ($response->isSuccess()) {
        $payments = $response->getItems();
        echo "✓ Recent payments: " . count($payments) . "\n";
    }

    $response = $client->finance()->getOperations([
        'limit' => 5,
    ]);
    if ($response->isSuccess()) {
        $operations = $response->getItems();
        echo "✓ Recent operations: " . count($operations) . "\n";
    }

    // =====================
    // SUMMARY
    // =====================
    echo "\n========================================\n";
    echo "  Workflow Demo Completed Successfully!\n";
    echo "========================================\n";
    echo "\nThis demo showed how to:\n";
    echo "1. ✓ Retrieve seller information\n";
    echo "2. ✓ Browse product categories and brands\n";
    echo "3. ✓ Check and manage offers\n";
    echo "4. ✓ Monitor and process orders\n";
    echo "5. ✓ Handle customer discussions\n";
    echo "6. ✓ View financial information\n";
    echo "\nRefer to individual sample files for detailed API usage.\n";

} catch (\Exception $e) {
    handleException($e);
}
