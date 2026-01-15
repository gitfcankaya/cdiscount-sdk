<?php

/**
 * Finance API Sample Script
 * 
 * This script demonstrates how to use the Finance API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Finance API Examples ===\n";

    // =====================
    // INVOICES
    // =====================

    // 1. Get Invoices
    echo "\n--- Getting Invoices ---\n";
    $response = $client->finance()->getInvoices([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        $invoices = $response->getItems();
        printResponse($invoices, 'Invoices');

        // If we have invoices, get details
        if (!empty($invoices)) {
            $invoiceId = $invoices[0]['id'] ?? $invoices[0]['invoiceId'] ?? null;

            if ($invoiceId) {
                // 2. Get Invoice Details
                echo "\n--- Getting Invoice Details ---\n";
                $response = $client->finance()->getInvoiceDetails($invoiceId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Invoice {$invoiceId} Details");
                }

                // 3. Download Invoice (commented - will download file)
                /*
                echo "\n--- Downloading Invoice ---\n";
                $response = $client->finance()->downloadInvoice($invoiceId);
                if ($response->isSuccess()) {
                    $pdfContent = $response->getRawBody();
                    file_put_contents("finance_invoice_{$invoiceId}.pdf", $pdfContent);
                    echo "Invoice PDF saved to finance_invoice_{$invoiceId}.pdf\n";
                }
                */
            }
        }
    }

    // =====================
    // OPERATIONS
    // =====================

    // 4. Get Operations
    echo "\n--- Getting Operations ---\n";
    $response = $client->finance()->getOperations([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        $operations = $response->getItems();
        printResponse($operations, 'Operations');

        // If we have operations, get details
        if (!empty($operations)) {
            $operationId = $operations[0]['id'] ?? $operations[0]['operationId'] ?? null;

            if ($operationId) {
                // 5. Get Operation Details
                echo "\n--- Getting Operation Details ---\n";
                $response = $client->finance()->getOperationDetails($operationId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Operation {$operationId} Details");
                }
            }
        }
    }

    // =====================
    // PAYMENTS
    // =====================

    // 6. Get Payments
    echo "\n--- Getting Payments ---\n";
    $response = $client->finance()->getPayments([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        $payments = $response->getItems();
        printResponse($payments, 'Payments');

        // If we have payments, get details
        if (!empty($payments)) {
            $paymentId = $payments[0]['id'] ?? $payments[0]['paymentId'] ?? null;

            if ($paymentId) {
                // 7. Get Payment Details
                echo "\n--- Getting Payment Details ---\n";
                $response = $client->finance()->getPaymentDetails($paymentId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Payment {$paymentId} Details");
                }
            }
        }
    }

    // =====================
    // REPORTS (DAC7)
    // =====================

    // 8. Get Reports
    echo "\n--- Getting Reports (DAC7) ---\n";
    $response = $client->finance()->getReports([
        'limit' => 10,
    ]);
    if ($response->isSuccess()) {
        $reports = $response->getItems();
        printResponse($reports, 'Reports');

        // If we have reports, get details
        if (!empty($reports)) {
            $reportId = $reports[0]['id'] ?? $reports[0]['reportId'] ?? null;

            if ($reportId) {
                // 9. Get Report Details
                echo "\n--- Getting Report Details ---\n";
                $response = $client->finance()->getReportDetails($reportId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Report {$reportId} Details");
                }

                // 10. Download Report (commented - will download file)
                /*
                echo "\n--- Downloading Report ---\n";
                $response = $client->finance()->downloadReport($reportId);
                if ($response->isSuccess()) {
                    $content = $response->getRawBody();
                    file_put_contents("report_{$reportId}.pdf", $content);
                    echo "Report saved to report_{$reportId}.pdf\n";
                }
                */
            }
        }
    }

    echo "\n=== Finance API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
