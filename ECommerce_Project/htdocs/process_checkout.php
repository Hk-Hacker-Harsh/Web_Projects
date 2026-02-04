<?php
ob_start();
session_start();

// 1. Setup & Security
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/stripe-php/init.php'; // Manual include for InfinityFree

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// 2. Stripe Configuration
$stripe_secret_key = '****'; // Replace with your ACTUAL Secret Key
\Stripe\Stripe::setApiKey($stripe_secret_key);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];
    $total_amount = $_POST['total_amount'];
    // Check if coupon exists, otherwise set to null or handle accordingly
    $coupon_id = isset($_POST['coupon_id']) ? $_POST['coupon_id'] : NULL;

    // --- STRIPE PAYMENT PATH ---
    if ($payment_method == 'stripe') {
        try {
            // Create a Stripe Checkout Session
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'HK Store - Order Payment',
                        ],
                        'unit_amount' => round($total_amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // Success URL passes session_id to success.php for verification
                'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/checkout.php',
            ]);

            // Redirect to Stripe
            header("Location: " . $checkout_session->url);
            exit();

        } catch (Exception $e) {
            die("Stripe Error: " . $e->getMessage());
        }

    } 
    // --- CASH ON DELIVERY (COD) PATH ---
    else {
        // Based on your table image: id(auto), user_id, total_amount, payment_method, status, created_at(auto)
        $status = "pending";
        
        // Corrected Query: Removed 'order_id' to match your DB image
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?, ?, 'cod', ?)");
        $stmt->bind_param("ids", $user_id, $total_amount, $status);
        
        if ($stmt->execute()) {
            $new_id = $conn->insert_id; // Get the auto-incremented ID

            // 3. Insert into 'order_items' table
            foreach ($_SESSION['cart'] as $cart_key => $qty) {
                $parts = explode('_', $cart_key);
                $p_id = (int)$parts[0];
                
                $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
                $item_stmt->bind_param("iii", $new_id, $p_id, $qty);
                $item_stmt->execute();
            }

            // 4. Cleanup and Redirect
            unset($_SESSION['cart']);
            unset($_SESSION['applied_coupon']);
            header("Location: success.php?method=cod");
            exit();
        } else {
            die("Database Error: " . $conn->error);
        }
    }
}