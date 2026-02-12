<?php
require_once 'db.php';
require_once 'vendor/autoload.php'; // If using Composer, otherwise use their manual include

// Use your SECRET KEY from the dashboard
require_once __DIR__ . '/config.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://ecoproject.infinityfree.me';

try {
    // Create a Checkout Session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => 2000, // Amount in cents ($20.00)
                'product_data' => [
                    'name' => 'Order #1234 from HK Store',
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.php',
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}