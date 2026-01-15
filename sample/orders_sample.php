<?php

/**
 * Orders API Sample Script
 * 
 * This script demonstrates how to use the Orders API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Orders API Examples ===\n";

    $salesChannelId = 'CDISFR';

    // 1. Get Orders Count
    echo "\n--- Getting Orders Count ---\n";
    $response = $client->orders()->getOrdersCount([
        'salesChannelId' => $salesChannelId,
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Orders Count');
    }

    // 2. Get Orders (paginated)
    echo "\n--- Getting Orders (First Page) ---\n";
    $response = $client->orders()->getOrders([
        'salesChannelId' => $salesChannelId,
        'pageIndex' => 1,
        'pageSize' => 10,
        'sort' => 'createdAt',
        'order' => 'desc',
    ]);
    if ($response->isSuccess()) {
        $orders = $response->getItems();
        printResponse($orders, 'Orders');
        echo "Has next page: " . ($response->hasNextPage() ? 'Yes' : 'No') . "\n";

        // If we have orders, demonstrate getting order details
        if (!empty($orders)) {
            $orderId = $orders[0]['id'] ?? $orders[0]['orderId'] ?? null;

            if ($orderId) {
                // 3. Get Specific Order
                echo "\n--- Getting Specific Order ---\n";
                $response = $client->orders()->getOrder($orderId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Order {$orderId}");
                }

                // 4. Get Order Lines
                echo "\n--- Getting Order Lines ---\n";
                $response = $client->orders()->getOrderLines($orderId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Order {$orderId} Lines");
                }
            }
        }
    }

    // 5. Get Cancellation Reasons
    echo "\n--- Getting Cancellation Reasons ---\n";
    $response = $client->orders()->getCancellationReasons($salesChannelId, 'Seller');
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Cancellation Reasons');
    }

    // 6. Get Commercial Gesture Requests
    echo "\n--- Getting Commercial Gesture Requests ---\n";
    $response = $client->orders()->getCommercialGestureRequests([
        'salesChannelId' => $salesChannelId,
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Commercial Gesture Requests');
    }

    // Example operations (commented - requires valid order)
    /*
    $orderId = 'SCID123456789';

    // 7. Validate Order
    echo "\n--- Validating Order ---\n";
    $response = $client->orders()->validateOrder($orderId, 'Accepted');
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Order Validated');
    }

    // 8. Ship Order
    echo "\n--- Shipping Order ---\n";
    $shipments = [
        [
            'parcelNumber' => 'TRACK123456789',
            'carrierCode' => 'colissimo',
            'items' => [
                [
                    'lineId' => 'LINE001',
                    'quantity' => 1,
                ],
            ],
        ],
    ];
    $response = $client->orders()->shipOrder($orderId, $shipments);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Order Shipped');
    }

    // 9. Create Cancellation Request
    echo "\n--- Creating Cancellation Request ---\n";
    $response = $client->orders()->createCancellationRequest(
        $orderId,
        'seller-out-of-stock',
        true
    );
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Cancellation Request Created');
    }

    // 10. Create Commercial Gesture Request
    echo "\n--- Creating Commercial Gesture Request ---\n";
    $response = $client->orders()->createCommercialGestureRequest(
        $orderId,
        'MissingProduct',
        12.50,
        'Product was missing from package'
    );
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Commercial Gesture Request Created');
    }
    */

    echo "\n=== Orders API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
