<?php
ob_start();
session_start();

// 1. Setup & Security
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/stripe-php/init.php';
require_once __DIR__ . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: signin.php");
    exit();
}

// 2. Stripe Configuration
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];
    
    // Capture and Sanitize Shipping Address (Mentor Security Requirement)
    $shipping_address = isset($_POST['shipping_address']) ? mysqli_real_escape_string($conn, $_POST['shipping_address']) : '';

    if (empty($shipping_address)) {
        die("Error: Shipping address is required.");
    }

    // --- SECURITY FIX: RECALCULATE TOTAL ON SERVER ---
    $server_calculated_total = 0;
    $items_to_verify = [];

    foreach ($_SESSION['cart'] as $cart_key => $qty) {
        $parts = explode('_', $cart_key);
        $p_id = (int)$parts[0];

        $stmt = $conn->prepare("SELECT price, name, stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if ($product) {
            $server_calculated_total += ($product['price'] * $qty);
            $items_to_verify[] = [
                'id' => $p_id, 
                'qty' => $qty, 
                'name' => $product['name'],
                'price' => $product['price']
            ];
        }
    }

    // Handle Coupons
    if (isset($_SESSION['applied_coupon'])) {
        $coupon_code = $_SESSION['applied_coupon']['code'];
        $c_stmt = $conn->prepare("SELECT discount_type, value FROM coupons WHERE code = ? AND expiry >= CURDATE()");
        $c_stmt->bind_param("s", $coupon_code);
        $c_stmt->execute();
        $coupon = $c_stmt->get_result()->fetch_assoc();
        
        if ($coupon) {
            if ($coupon['discount_type'] == 'percentage') {
                $server_calculated_total -= ($server_calculated_total * ($coupon['value'] / 100));
            } else {
                $server_calculated_total -= $coupon['value'];
            }
        }
    }

    $server_calculated_total = max(0, $server_calculated_total);

    // --- STRIPE PAYMENT PATH ---
    if ($payment_method == 'stripe') {
        try {
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => ['name' => 'HK Store - Order Payment'],
                        'unit_amount' => round($server_calculated_total * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // Updated Metadata to include shipping address for Webhook processing
                'metadata' => [
                    'user_id' => $user_id,
                    'cart_data' => json_encode($_SESSION['cart']),
                    'shipping_address' => $shipping_address, // Pass to webhook
                    'coupon_id' => isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon']['id'] : null
                ],
                'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/checkout.php',
            ]);

            header("Location: " . $checkout_session->url);
            exit();
        } catch (Exception $e) {
            die("Stripe Error: " . $e->getMessage());
        }
    } 
    // --- CASH ON DELIVERY (COD) PATH WITH STOCK LOCKING ---
    else {
        $conn->begin_transaction();

        try {
            $status = "pending";
            // Updated Query to include shipping_address
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, status) VALUES (?, ?, ?, 'cod', ?)");
            $stmt->bind_param("idss", $user_id, $server_calculated_total, $shipping_address, $status);
            $stmt->execute();
            $new_id = $conn->insert_id;

            foreach ($items_to_verify as $item) {
                $lock_stmt = $conn->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
                $lock_stmt->bind_param("i", $item['id']);
                $lock_stmt->execute();
                $current_stock = $lock_stmt->get_result()->fetch_assoc()['stock'];

                if ($current_stock < $item['qty']) {
                    throw new Exception("Insufficient stock for " . $item['name']);
                }

                $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update_stmt->bind_param("ii", $item['qty'], $item['id']);
                $update_stmt->execute();

                $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
                $item_stmt->bind_param("iii", $new_id, $item['id'], $item['qty']);
                $item_stmt->execute();
            }

            $conn->commit();
            unset($_SESSION['cart']);
            unset($_SESSION['applied_coupon']);
            header("Location: success.php?method=cod");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            die("Order Error: " . $e->getMessage());
        }
    }
}