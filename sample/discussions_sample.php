<?php

/**
 * Discussions API Sample Script
 * 
 * This script demonstrates how to use the Discussions API endpoints.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    echo "=== Discussions API Examples ===\n";

    $salesChannel = 'CDISFR';

    // 1. Get Discussions Count
    echo "\n--- Getting Discussions Count ---\n";
    $response = $client->discussions()->getDiscussionsCount([
        'salesChannel' => $salesChannel,
    ]);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Discussions Count');
    }

    // 2. Get Discussions (paginated)
    echo "\n--- Getting Discussions ---\n";
    $response = $client->discussions()->getDiscussions([
        'salesChannel' => $salesChannel,
        'pageSize' => 10,
    ]);
    if ($response->isSuccess()) {
        $discussions = $response->getItems();
        printResponse($discussions, 'Discussions');

        // If we have discussions, demonstrate getting specific discussion
        if (!empty($discussions)) {
            $discussionId = $discussions[0]['id'] ?? $discussions[0]['discussionId'] ?? null;

            if ($discussionId) {
                // 3. Get Specific Discussion
                echo "\n--- Getting Specific Discussion ---\n";
                $response = $client->discussions()->getDiscussion($discussionId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Discussion {$discussionId}");
                }

                // 4. Get Discussion Messages
                echo "\n--- Getting Discussion Messages ---\n";
                $response = $client->discussions()->getMessages($discussionId);
                if ($response->isSuccess()) {
                    printResponse($response->getData(), "Discussion {$discussionId} Messages");
                }
            }
        }
    }

    // 5. Get Discussion Typologies
    echo "\n--- Getting Discussion Typologies ---\n";
    $response = $client->discussions()->getTypologies($salesChannel);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Discussion Typologies');
    }

    // 6. Get Discussion Sub-typologies
    echo "\n--- Getting Discussion Sub-typologies ---\n";
    $response = $client->discussions()->getSubtypologies($salesChannel);
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Discussion Sub-typologies');
    }

    // Example operations (commented - requires valid data)
    /*
    // 7. Create a New Discussion
    echo "\n--- Creating New Discussion ---\n";
    $response = $client->discussions()->createDiscussion([
        'subject' => 'Product Quality Issue',
        'orderId' => 'SCID123456789',
        'salesChannel' => $salesChannel,
        'subtypologyCode' => 'broken-product',
        'message' => [
            'body' => 'The product I received appears to be damaged.',
            'receiver' => 'Customer',
        ],
    ]);
    if ($response->isSuccess()) {
        $newDiscussionId = $response->getData()['id'] ?? null;
        printResponse($response->getData(), 'New Discussion Created');

        if ($newDiscussionId) {
            // 8. Send a Message
            echo "\n--- Sending Message ---\n";
            $response = $client->discussions()->sendMessage(
                $newDiscussionId,
                'Thank you for contacting us. We will investigate this issue and get back to you shortly.',
                'Customer'
            );
            if ($response->isSuccess()) {
                printResponse($response->getData(), 'Message Sent');
            }

            // 9. Close Discussion
            echo "\n--- Closing Discussion ---\n";
            $response = $client->discussions()->closeDiscussion($newDiscussionId);
            if ($response->isSuccess()) {
                echo "Discussion closed successfully.\n";
            }
        }
    }

    // 10. Upload Attachment
    echo "\n--- Uploading Attachment ---\n";
    $discussionId = '507f1f77bcf86cd799439011';
    $response = $client->discussions()->uploadAttachment(
        $discussionId,
        'path/to/attachment.pdf',
        'attachment.pdf'
    );
    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Attachment Uploaded');
    }
    */

    echo "\n=== Discussions API Examples Completed ===\n";

} catch (\Exception $e) {
    handleException($e);
}
