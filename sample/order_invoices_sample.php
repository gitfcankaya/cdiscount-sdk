<?php

/**
 * Order Invoices API Sample Script
 * 
 * This script demonstrates how to use the Order Invoices API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Order Invoices API Examples ===\n";

    // 1. Get Order Invoices
    echo "\n--- Getting Order Invoices ---\n";
    $response = $client->orderInvoices()->getOrderInvoices([
        'pageIndex' => 1,
        'pageSize' => 10,
    ]);
    if ($response->isSuccess()) {
        $invoices = $response->getItems();
        printResponse($invoices, 'Order Invoices');

        // If we have invoices, demonstrate getting specific invoice
        if (!empty($invoices)) {
            $invoiceId = $invoices[0]['id'] ?? $invoices[0]['invoiceId'] ?? null;

            if ($invoiceId) {
                // 2. Get Specific Order Invoice
                echo "\n--- Getting Specific Order Invoice ---\n";
                $response = $client->orderInvoices()->getOrderInvoice($invoiceId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Invoice {$invoiceId}");
                }

                // 3. Download Invoice PDF (commented - will download file)
                /*
                echo "\n--- Downloading Invoice PDF ---\n";
                $response = $client->orderInvoices()->downloadInvoice($invoiceId);
                if ($response->isSuccess()) {
                    $pdfContent = $response->getRawBody();
                    file_put_contents("invoice_{$invoiceId}.pdf", $pdfContent);
                    echo "Invoice PDF saved to invoice_{$invoiceId}.pdf\n";
                }
                */
            }
        }
    }

    // 4. Upload Invoice Example (commented - requires valid data)
    /*
    echo "\n--- Uploading Invoice ---\n";
    $orderId = 'SCID123456789';
    $invoiceData = [
        'invoiceNumber' => 'INV-2026-001',
        'invoiceDate' => '2026-01-15',
        'file' => base64_encode(file_get_contents('path/to/invoice.pdf')),
        'fileName' => 'invoice.pdf',
        'mimeType' => 'application/pdf',
    ];
    $response = $client->orderInvoices()->uploadInvoice($orderId, $invoiceData);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Invoice Uploaded');
    }
    */

    echo "\n=== Order Invoices API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
