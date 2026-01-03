# Laravel Forwarder

A Laravel package to easily handle incoming Forwarder webhooks.

## Installation

You can install the package via composer:

```bash
composer require theaarch64/laravel-forwarder:dev-main
```

After installing, run the install command to scaffold the necessary files:

```bash
php artisan forwarder:install
```

This command will:
1. Publish the configuration file to `config/forwarder.php`.
2. Publish the webhook handler action to `app/Actions/Forwarder/HandleWebhook.php`.
3. Publish and register the `ForwarderServiceProvider` in your application.

## Usage

### Handling Webhooks

The package uses an Action class to handle incoming webhooks. After installation, you can find the handler at `app/Actions/Forwarder/HandleWebhook.php`.

You should modify the `handle` method in this class to implement your custom logic (e.g., saving the message to the database, forwarding it to Telegram/Slack, etc.).

```php
<?php

namespace App\Actions\Forwarder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Theaarch\Forwarder\Contracts\HandlesWebhooks;

class HandleWebhook implements HandlesWebhooks
{
    /**
     * Handle a webhook call.
     *
     * @param  Request  $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        Log::info('Webhook Received', $request->all());

        // Your logic here...

        return new Response('Webhook Handled', Response::HTTP_OK);
    }
}
```

### Configuration

You can configure the package in `config/forwarder.php`.

#### Route Prefix
By default, the webhook route is available at `/forwarder/webhook`. You can change the prefix in the config or via `.env`:

```env
FORWARDER_PREFIX=custom-prefix
```

#### Middleware
You can add custom middleware to the webhook route in `config/forwarder.php`:

```php
'middleware' => ['api'],
```

### Security: Webhook Signature Verification

To ensure that the webhook requests are coming from a trusted source, you should configure a webhook secret.

1. Set the secret in your `.env` file:
   ```env
   FORWARDER_WEBHOOK_SECRET=your-secret-key
   ```

2. The package will automatically verify the signature included in the request body (`sign` parameter) using this secret.

If the secret is not set, signature verification is skipped (not recommended for production).

## Testing

You can run the tests with:

```bash
vendor/bin/pest
```

## License

The MIT License (MIT).
