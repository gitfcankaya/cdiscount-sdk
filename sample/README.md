# CDiscount SDK Sample Scripts

This folder contains sample PHP scripts demonstrating how to use the CDiscount Octopia SDK.

## Prerequisites

1. Install dependencies:

   ```bash
   cd cdiscount_sdk_v2
   composer install
   ```

2. Create configuration file:

   ```bash
   cp config.example.json config.json
   ```

3. Edit `config.json` with your credentials:
   ```json
   {
     "client_id": "YOUR_CLIENT_ID",
     "client_secret": "YOUR_CLIENT_SECRET",
     "grant_type": "client_credentials",
     "base_url_token": "https://auth.octopia-io.net",
     "base_url": "https://api.octopia-io.net/seller/v2",
     "seller_id": "YOUR_SELLER_ID"
   }
   ```

## Available Samples

| Script                         | Description                                          |
| ------------------------------ | ---------------------------------------------------- |
| `bootstrap.php`                | Common initialization file (required by all samples) |
| `seller_sample.php`            | Seller information and configuration                 |
| `products_sample.php`          | Product catalog and category management              |
| `offers_sample.php`            | Offer creation and management                        |
| `orders_sample.php`            | Order processing and shipment                        |
| `order_invoices_sample.php`    | Order invoice management                             |
| `discussions_sample.php`       | Customer discussions and messaging                   |
| `fulfillment_sample.php`       | Octopia Fulfillment operations                       |
| `finance_sample.php`           | Financial information and reports                    |
| `complete_workflow_sample.php` | Complete e-commerce workflow demo                    |

## Running Samples

```bash
# Run from the sample directory
cd sample

# Run individual samples
php seller_sample.php
php products_sample.php
php offers_sample.php
php orders_sample.php
php order_invoices_sample.php
php discussions_sample.php
php fulfillment_sample.php
php finance_sample.php

# Run complete workflow demo
php complete_workflow_sample.php
```

## Sample Structure

Each sample script follows this structure:

```php
<?php

require_once __DIR__ . '/bootstrap.php';

try {
    $client = createClient();

    // API calls...
    $response = $client->orders()->getOrders([...]);

    if ($response->isSuccess()) {
        printResponse($response->getData(), 'Title');
    }

} catch (\Exception $e) {
    handleException($e);
}
```

## Helper Functions

The `bootstrap.php` file provides these helper functions:

| Function                       | Description                                 |
| ------------------------------ | ------------------------------------------- |
| `createClient()`               | Creates and returns a configured SDK client |
| `printResponse($data, $title)` | Pretty prints response data                 |
| `handleException($e)`          | Handles and displays exceptions             |

## Notes

- Some operations (create, update, delete) are commented out in the samples to prevent accidental modifications.
- Uncomment and modify these operations as needed for your testing.
- Always test with non-production data first.

## Error Handling

All samples include error handling for:

- `AuthenticationException` - Authentication failures
- `ApiException` - API request errors
- `ValidationException` - Data validation errors

## Support

For API documentation, visit [Octopia Help Center](https://help.octopia.com/).
