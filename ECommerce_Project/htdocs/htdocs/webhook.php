<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/stripe-php/init.php';

// Set your Webhook Secret from Stripe Dashboard
$endpoint_secret = STRIPE_WEBHOOK_SECRET; // You get this from Stripe Dashboard > Developers > Webhooks

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch(\UnexpectedValueException $e) {
    http_response_code(400); exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400); exit();
}

// Handle the checkout.session.completed event
if ($event->type == 'checkout.session.completed') {
    $session = $event->data->object;
    
    // Retrieve metadata we sent during checkout
    $user_id = $session->metadata->user_id;
    $cart_json = $session->metadata->cart_data;
    $total = $session->amount_total / 100; // Convert cents to dollars
    
    $cart = json_decode($cart_json, true);

    // START TRANSACTION TO SAVE ORDER
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?, ?, 'stripe', 'paid')");
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;

        foreach ($cart as $cart_key => $qty) {
            $p_id = (int)explode('_', $cart_key)[0];
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $item_stmt->bind_param("iii", $order_id, $p_id, $qty);
            $item_stmt->execute();
            
            // Deduct Stock
            $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $p_id");
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Webhook Order Save Failed: " . $e->getMessage());
    }
}

http_response_code(200);