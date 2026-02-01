<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

// 1. Basic Security
if ($_SERVER["REQUEST_METHOD"] != "POST" || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_amount = mysqli_real_escape_string($conn, $_POST['total_amount']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$status = ($payment_method == 'cod') ? 'pending' : 'paid'; 

// 2. Insert into the main 'orders' table
$order_query = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
                VALUES ($user_id, '$total_amount', '$payment_method', '$status')";

if (mysqli_query($conn, $order_query)) {
    $new_order_id = mysqli_insert_id($conn); // Get the ID of the order we just created

    // 3. AUTOMATIC UPDATE: Loop through the cart and save items
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        
        // Fetch current product price (Best practice for data science: keep a price snapshot)
        $prod_res = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
        $product = mysqli_fetch_assoc($prod_res);
        $current_price = $product['price'];

        // Insert into order_items table
        $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                       VALUES ($new_order_id, $product_id, $quantity, '$current_price')";
        
        mysqli_query($conn, $item_query);

        // 4. Update Product Stock (Optional but recommended)
        mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
    }

    // 5. Cleanup
    unset($_SESSION['cart']);
    unset($_SESSION['applied_coupon']);

    header("Location: order_details.php?id=" . $new_order_id . "&msg=success");
    exit();
} else {
    echo "Error placing order: " . mysqli_error($conn);
}