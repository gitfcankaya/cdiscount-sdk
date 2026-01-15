<?php

/**
 * Fulfillment API Sample Script
 * 
 * This script demonstrates how to use the Fulfillment (Octopia Fulfillment) API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Fulfillment API Examples ===\n";

    // =====================
    // INBOUND SHIPMENTS
    // =====================

    // 1. Get Inbound Shipments
    echo "\n--- Getting Inbound Shipments ---\n";
    $response = $client->fulfillment()->getInboundShipments([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        $shipments = $response->getItems();
        printResponse($shipments, 'Inbound Shipments');

        // If we have shipments, get details
        if (!empty($shipments)) {
            $shipmentId = $shipments[0]['id'] ?? $shipments[0]['shipmentId'] ?? null;

            if ($shipmentId) {
                // 2. Get Specific Inbound Shipment
                echo "\n--- Getting Specific Inbound Shipment ---\n";
                $response = $client->fulfillment()->getInboundShipment($shipmentId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Inbound Shipment {$shipmentId}");
                }

                // 3. Get Inbound Shipment Items
                echo "\n--- Getting Inbound Shipment Items ---\n";
                $response = $client->fulfillment()->getInboundShipmentItems($shipmentId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Inbound Shipment {$shipmentId} Items");
                }
            }
        }
    }

    // =====================
    // STOCKS
    // =====================

    // 4. Get Stocks
    echo "\n--- Getting Stocks ---\n";
    $response = $client->fulfillment()->getStocks([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getItems(), 'Stocks');
    }

    // =====================
    // OUTBOUND SHIPMENTS
    // =====================

    // 5. Get Outbound Shipments
    echo "\n--- Getting Outbound Shipments ---\n";
    $response = $client->fulfillment()->getOutboundShipments([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        $outboundShipments = $response->getItems();
        printResponse($outboundShipments, 'Outbound Shipments');

        // If we have outbound shipments, get details
        if (!empty($outboundShipments)) {
            $outboundId = $outboundShipments[0]['id'] ?? $outboundShipments[0]['shipmentId'] ?? null;

            if ($outboundId) {
                // 6. Get Specific Outbound Shipment
                echo "\n--- Getting Specific Outbound Shipment ---\n";
                $response = $client->fulfillment()->getOutboundShipment($outboundId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Outbound Shipment {$outboundId}");
                }
            }
        }
    }

    // =====================
    // RETURNS
    // =====================

    // 7. Get Returns
    echo "\n--- Getting Returns ---\n";
    $response = $client->fulfillment()->getReturns([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        $returns = $response->getItems();
        printResponse($returns, 'Returns');

        // If we have returns, get details
        if (!empty($returns)) {
            $returnId = $returns[0]['id'] ?? $returns[0]['returnId'] ?? null;

            if ($returnId) {
                // 8. Get Specific Return
                echo "\n--- Getting Specific Return ---\n";
                $response = $client->fulfillment()->getReturn($returnId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Return {$returnId}");
                }
            }
        }
    }

    // =====================
    // CREATE OPERATIONS (commented)
    // =====================

    /*
    // 9. Create Inbound Shipment
    echo "\n--- Creating Inbound Shipment ---\n";
    $shipmentData = [
        'items' => [
            [
                'sku' => 'SKU001',
                'quantity' => 100,
            ],
            [
                'sku' => 'SKU002',
                'quantity' => 50,
            ],
        ],
        'expectedDeliveryDate' => '2026-02-01',
    ];
    $response = $client->fulfillment()->createInboundShipment($shipmentData);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Inbound Shipment Created');
    }

    // 10. Create Outbound Shipment
    echo "\n--- Creating Outbound Shipment ---\n";
    $outboundData = [
        'items' => [
            [
                'sku' => 'SKU001',
                'quantity' => 10,
            ],
        ],
        'shippingAddress' => [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'street' => '123 Main St',
            'city' => 'Paris',
            'postalCode' => '75001',
            'country' => 'FR',
        ],
    ];
    $response = $client->fulfillment()->createOutboundShipment($outboundData);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Outbound Shipment Created');
    }
    */

    echo "\n=== Fulfillment API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
