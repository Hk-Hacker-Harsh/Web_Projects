<?php
ob_start();
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/stripe-php/init.php';
require_once __DIR__ . '/includes/header.php';

// 1. Stripe Configuration
require_once __DIR__ . '/config.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$order_placed = false;
$error_msg = "";
$db_id = "";

// 2. Process Stripe Success
if (isset($_GET['session_id']) && !empty($_SESSION['cart'])) {
    $session_id = $_GET['session_id'];

    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);

        if ($session->payment_status == 'paid') {
            $user_id = $_SESSION['user_id'];
            $total_payable = $session->amount_total / 100; // Stripe cents to decimal

            // Match your table: id (auto), user_id, total_amount, payment_method, status, created_at (auto)
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?, ?, 'stripe', 'paid')");
            $stmt->bind_param("id", $user_id, $total_payable);
            
            if ($stmt->execute()) {
                $db_id = $conn->insert_id; // This is the 'id' from your image

                // Handle Order Items (Assuming you have an order_items table)
                foreach ($_SESSION['cart'] as $cart_key => $qty) {
                    $parts = explode('_', $cart_key);
                    $p_id = (int)$parts[0];
                    
                    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
                    $item_stmt->bind_param("iii", $db_id, $p_id, $qty);
                    $item_stmt->execute();
                }

                // Cleanup
                unset($_SESSION['cart']);
                unset($_SESSION['applied_coupon']);
                $order_placed = true;
            }
        }
    } catch (Exception $e) {
        $error_msg = "Verification failed: " . $e->getMessage();
    }
} 
// 3. Process COD Success
elseif (isset($_GET['method']) && $_GET['method'] == 'cod') {
    $order_placed = true;
    // For COD, the order is usually inserted in process_checkout.php
    // We just show the success UI here.
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <?php if ($order_placed): ?>
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h1 class="fw-bold">Order Successful!</h1>
                        <p class="text-muted mb-4">Your payment has been verified and your order is now being processed.</p>
                        
                        <?php if($db_id): ?>
                        <div class="bg-light p-3 rounded-3 mb-4 border">
                            <span class="text-uppercase small fw-bold text-muted d-block">Order ID</span>
                            <span class="h4 fw-bold text-primary">#<?php echo $db_id; ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-dark btn-lg rounded-pill">Return Home</a>
                        </div>

                    <?php else: ?>
                        <div class="mb-4">
                            <i class="bi bi-exclamation-octagon-fill text-danger" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="fw-bold">Something went wrong</h2>
                        <p class="text-muted mb-4">
                            <?php echo $error_msg ? $error_msg : "We couldn't confirm your order. Please check your dashboard."; ?>
                        </p>
                        <a href="checkout.php" class="btn btn-outline-danger rounded-pill px-5">Go Back</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/includes/footer.php'; 
ob_end_flush();
?>