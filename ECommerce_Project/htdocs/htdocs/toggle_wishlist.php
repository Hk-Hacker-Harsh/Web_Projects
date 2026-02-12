<?php
session_start();
require_once __DIR__ . '/db.php';

// 1. Authentication Check
// If the user isn't logged in, they shouldn't be able to save items.
if (!isset($_SESSION['user_id'])) {
    // We send them back with a message
    echo "<script>
        alert('Please login to save items to your wishlist.');
        window.location.href = 'signin.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Validate Product ID
// We use POST to prevent people from accidentally triggering this via a simple link (GET)
if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    // 3. Check if the item is already in the wishlist
    $check_query = "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Already exists? REMOVE IT (Toggle off)
        $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        mysqli_query($conn, $delete_query);
    } else {
        // Doesn't exist? ADD IT (Toggle on)
        $insert_query = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";
        mysqli_query($conn, $insert_query);
    }

    // 4. Redirect Back
    // This sends the user back to the product details page they were just on
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: product_details.php?id=" . $product_id);
    }
    exit();
    
} else {
    // If no product_id was sent, go back to shop
    header("Location: product.php");
    exit();
}