# CDiscount Octopia SDK for PHP

PHP SDK for the Octopia Seller API V2 (CDiscount Marketplace Integration).

## Requirements

- PHP 7.4 or higher
- PHP cURL extension
- PHP JSON extension

## Installation

### Using Composer

```bash
composer require cdiscount/octopia-sdk
```

Or add to your `composer.json`:

```json
{
  "require": {
    "cdiscount/octopia-sdk": "^1.0"
  }
}
```

Then run:

```bash
composer install
```

## Configuration

Create a configuration file `config.json`:

```json
{
  "client_id": "YOUR_CLIENT_ID",
  "client_secret": "YOUR_CLIENT_SECRET",
  "grant_type": "client_credentials",
  "base_url_token": "https://auth.octopia-io.net",
  "base_url": "https://api.octopia-io.net/seller/v2",
  "seller_id": "YOUR_SELLER_ID",
  "timeout": 30,
  "debug": false
}
```

## Usage

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use CDiscount\Sdk\CDiscountClient;

// Create client from configuration array
$client = CDiscountClient::create([
    'client_id' => 'YOUR_CLIENT_ID',
    'client_secret' => 'YOUR_CLIENT_SECRET',
    'grant_type' => 'client_credentials',
    'base_url_token' => 'https://auth.octopia-io.net',
    'seller_id' => 'YOUR_SELLER_ID',
]);

// Or create from JSON config file
$client = CDiscountClient::fromConfigFile('config.json');

// Authentication is automatic, but you can manually authenticate
$token = $client->authenticate();
```

### Seller API

```php
// Get seller information
$response = $client->seller()->getSeller();
$sellerInfo = $response->getData();

// Get seller addresses
$response = $client->seller()->getAddresses();

// Get seller indicators
$response = $client->seller()->getIndicators();

// Get seller subscriptions
$response = $client->seller()->getSubscriptions();

// Get carriers
$response = $client->seller()->getCarriers();
```

### Products API

```php
// Get categories count
$response = $client->products()->getCategoriesCount('fr-FR');

// Get categories
$response = $client->products()->getCategories([
    'pageIndex' => 1,
    'pageSize' => 25,
    'fields' => 'label,level,isActive',
], 'fr-FR');

// Get specific category
$response = $client->products()->getCategory('010H0R', 'fr-FR');

// Get category properties
$response = $client->products()->getCategoryProperties('010H0R', 'fr-FR');

// Get brands
$response = $client->products()->getBrands(1, 25, 'name');

// Get products
$response = $client->products()->getProducts([
    'categoryReference' => '010101,010102',
    'limit' => 25,
]);

// Submit products for integration
$response = $client->products()->submitProducts([
    [
        'gtin' => '1234567890128',
        'sellerProductReference' => 'AGRA39401',
        'title' => 'My Product Title',
        'description' => 'My product description',
        'brand' => 'My Brand',
        'categoryCode' => '010201',
    ],
], 'fr-FR');

// Get product integration reports
$response = $client->products()->getProductIntegrationReports([
    'packageId' => 'd8cccfa2-b2d0-431a-a627-e078febc78a7',
]);
```

### Offers API

```php
// Create an offer package
$response = $client->offers()->createPackage('Upsert', 'CDISFR', 'en-US');
$packageId = $response->getHeader('Content-Location');

// Get offer packages
$response = $client->offers()->getPackages([
    'state' => 'Integrated',
    'salesChannelId' => 'CDISFR',
    'limit' => 100,
]);

// Upload offer requests to package
$response = $client->offers()->uploadOfferRequests($packageId, [
    [
        'sellerExternalReference' => 'SellerRef001',
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
    ],
]);

// Submit package for processing
$response = $client->offers()->submitPackage($packageId);

// Get offer request results
$response = $client->offers()->getOfferRequestResults($packageId);

// Get offers
$response = $client->offers()->getOffers('CDISFR', [
    'limit' => 1000,
    'fields' => 'condition,product,integrationPrice',
    'expand' => 'salesChannelFeedback',
]);
```

### Orders API

```php
// Get orders count
$response = $client->orders()->getOrdersCount([
    'salesChannelId' => 'CDISFR',
    'status' => 'InPreparation',
]);

// Get orders
$response = $client->orders()->getOrders([
    'salesChannelId' => 'CDISFR',
    'status' => 'Accepted,InPreparation',
    'pageIndex' => 1,
    'pageSize' => 50,
    'sort' => 'createdAt',
]);

// Get specific order
$response = $client->orders()->getOrder('SCID123456789');

// Validate/Accept an order
$response = $client->orders()->validateOrder('SCID123456789', 'Accepted');

// Ship an order
$response = $client->orders()->shipOrder('SCID123456789', [
    [
        'parcelNumber' => 'TRACK123456',
        'carrierCode' => 'colissimo',
        'items' => [
            [
                'lineId' => 'LINE001',
                'quantity' => 1,
            ],
        ],
    ],
]);

// Get cancellation reasons
$response = $client->orders()->getCancellationReasons('CDISFR', 'Seller', 'Shipped');

// Create cancellation request
$response = $client->orders()->createCancellationRequest(
    'SCID123456789',
    'seller-out-of-stock',
    true
);

// Create partial cancellation request
$response = $client->orders()->createPartialCancellationRequest(
    'SCID123456789',
    [
        [
            'lineId' => 'LINE001',
            'reason' => 'seller-out-of-stock',
        ],
    ]
);

// Get commercial gesture requests
$response = $client->orders()->getCommercialGestureRequests([
    'orderId' => 'SCID123456789',
]);

// Create commercial gesture request
$response = $client->orders()->createCommercialGestureRequest(
    'SCID123456789',
    'MissingProduct',
    12.50,
    'Product was missing from package'
);
```

### Discussions API

```php
// Get discussions
$response = $client->discussions()->getDiscussions([
    'salesChannel' => 'CDISFR',
    'isOpen' => true,
    'pageSize' => 50,
]);

// Get specific discussion
$response = $client->discussions()->getDiscussion('507f1f77bcf86cd799439011');

// Create a new discussion
$response = $client->discussions()->createDiscussion([
    'subject' => 'Product Issue',
    'orderId' => 'SCID123456789',
    'salesChannel' => 'CDISFR',
    'subtypologyCode' => 'broken-product',
    'message' => [
        'body' => 'The product received is broken.',
        'receiver' => 'Customer',
    ],
]);

// Send a message
$response = $client->discussions()->sendMessage(
    '507f1f77bcf86cd799439011',
    'Thank you for your message. We will resolve this issue.',
    'Customer'
);

// Close a discussion
$response = $client->discussions()->closeDiscussion('507f1f77bcf86cd799439011');
```

### Fulfillment API

```php
// Get inbound shipments
$response = $client->fulfillment()->getInboundShipments();

// Create inbound shipment
$response = $client->fulfillment()->createInboundShipment([
    // shipment data
]);

// Get stocks
$response = $client->fulfillment()->getStocks();

// Get outbound shipments
$response = $client->fulfillment()->getOutboundShipments();

// Get returns
$response = $client->fulfillment()->getReturns();
```

### Finance API

```php
// Get invoice details
$response = $client->finance()->getInvoiceDetails('INV123456');

// Get operations
$response = $client->finance()->getOperations();

// Get payments
$response = $client->finance()->getPayments();

// Get reports (DAC7)
$response = $client->finance()->getReports();
```

## Handling Responses

All API methods return an `ApiResponse` object:

```php
$response = $client->orders()->getOrders([
    'salesChannelId' => 'CDISFR',
]);

// Check if request was successful
if ($response->isSuccess()) {
    // Get response data
    $data = $response->getData();

    // Get items from paginated response
    $items = $response->getItems();

    // Get items per page
    $itemsPerPage = $response->getItemsPerPage();

    // Check for next page
    if ($response->hasNextPage()) {
        $nextPageUrl = $response->getNextPageUrl();
    }
}

// Get HTTP status code
$statusCode = $response->getStatusCode();

// Get response headers
$headers = $response->getHeaders();

// Get raw response body
$rawBody = $response->getRawBody();
```

## Error Handling

```php
use CDiscount\Sdk\Exception\ApiException;
use CDiscount\Sdk\Exception\AuthenticationException;
use CDiscount\Sdk\Exception\ValidationException;

try {
    $response = $client->orders()->getOrder('INVALID_ORDER');
} catch (AuthenticationException $e) {
    // Handle authentication errors
    echo "Authentication failed: " . $e->getMessage();
} catch (ApiException $e) {
    // Handle API errors
    echo "API Error: " . $e->getMessage();
    echo "Status Code: " . $e->getStatusCode();
    echo "Details: " . print_r($e->getDetails(), true);
    echo "Trace ID: " . $e->getTraceId();
} catch (ValidationException $e) {
    // Handle validation errors
    echo "Validation failed: " . $e->getMessage();
    echo "Errors: " . print_r($e->getErrors(), true);
}
```

## API Reference

### Available APIs

| API               | Description                                            |
| ----------------- | ------------------------------------------------------ |
| `seller()`        | Seller configuration and information                   |
| `products()`      | Product management (categories, brands, products)      |
| `offers()`        | Offer management (packages, requests, results)         |
| `orders()`        | Order management (orders, shipments, cancellations)    |
| `orderInvoices()` | Order invoice management                               |
| `discussions()`   | Discussion/messaging management                        |
| `fulfillment()`   | Octopia Fulfillment management                         |
| `finance()`       | Financial information (invoices, operations, payments) |

### Configuration Options

| Option           | Type   | Default                                | Description                     |
| ---------------- | ------ | -------------------------------------- | ------------------------------- |
| `client_id`      | string | -                                      | OAuth2 client ID (required)     |
| `client_secret`  | string | -                                      | OAuth2 client secret (required) |
| `grant_type`     | string | `client_credentials`                   | OAuth2 grant type               |
| `base_url_token` | string | `https://auth.octopia-io.net`          | Authentication server URL       |
| `base_url`       | string | `https://api.octopia-io.net/seller/v2` | API base URL                    |
| `seller_id`      | string | -                                      | Seller ID for requests          |
| `timeout`        | int    | `30`                                   | Request timeout in seconds      |
| `debug`          | bool   | `false`                                | Enable debug mode               |

## License

MIT License. See [LICENSE](LICENSE) file for details.

## Support

For API support, please visit [Octopia Help Center](https://help.octopia.com/).
