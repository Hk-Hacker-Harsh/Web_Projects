<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/db.php';

// 1. Handle Coupon Logic
$discount_amount = 0;
$coupon_msg = "";

// Apply Coupon
if (isset($_POST['apply_coupon'])) {
    $code = strtoupper(mysqli_real_escape_string($conn, $_POST['coupon_code']));
    $today = date('Y-m-d');
    
    $c_query = "SELECT * FROM coupons WHERE code = '$code' AND expiry >= '$today' LIMIT 1";
    $c_res = mysqli_query($conn, $c_query);
    
    if ($coupon = mysqli_fetch_assoc($c_res)) {
        $_SESSION['applied_coupon'] = $coupon;
        $coupon_msg = "<div class='text-success small'>Coupon applied!</div>";
    } else {
        unset($_SESSION['applied_coupon']);
        $coupon_msg = "<div class='text-danger small'>Invalid or expired coupon.</div>";
    }
}

// Remove Coupon
if (isset($_GET['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    header("Location: cart.php");
    exit();
}

echo '<div class="container mt-5"><h2>Your Shopping Cart</h2>';

if (!empty($_SESSION['cart'])) {
    // Flag to track if we can proceed to checkout
    $can_proceed = true;

    echo '<table class="table align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th style="width: 150px;">Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';
    
    $subtotal_sum = 0;

    foreach ($_SESSION['cart'] as $cart_key => $qty) {
        $parts = explode('_', $cart_key);
        $p_id = (int)$parts[0];

        // Fetch Base Product and its current Stock
        $p_query = "SELECT * FROM products WHERE id = $p_id";
        $p_res = mysqli_query($conn, $p_query);
        $product = mysqli_fetch_assoc($p_res);

        if (!$product) continue;

        // --- STOCK CHECK LOGIC ---
        $stock_warning = "";
        if ($product['stock'] < $qty) {
            $can_proceed = false; // Block checkout
            $stock_warning = "<div class='text-danger fw-bold small'><i class='bi bi-exclamation-triangle'></i> Only {$product['stock']} left in stock!</div>";
        }
        // -------------------------

        $unit_price = $product['price'];
        $variation_info = "";

        for ($i = 1; $i < count($parts); $i++) {
            $v_id = (int)$parts[$i];
            if ($v_id > 0) {
                $v_query = "SELECT variation_name, variation_value, price_modifier FROM product_variations WHERE id = $v_id";
                $v_res = mysqli_query($conn, $v_query);
                if ($v_data = mysqli_fetch_assoc($v_res)) {
                    $unit_price += $v_data['price_modifier'];
                    $variation_info .= "<br><small class='text-muted'>{$v_data['variation_name']}: {$v_data['variation_value']}</small>";
                }
            }
        }

        $item_total = $unit_price * $qty;
        $subtotal_sum += $item_total;

        echo "<tr>
                <td>
                    <strong>" . htmlspecialchars($product['name']) . "</strong>
                    $variation_info
                    $stock_warning
                </td>
                <td>\$" . number_format($unit_price, 2) . "</td>
                <td>
                    <div class='btn-group btn-group-sm'>
                        <a href='update_cart.php?id=$cart_key&action=dec' class='btn btn-outline-secondary'>-</a>
                        <span class='btn border-secondary disabled'>$qty</span>
                        <a href='update_cart.php?id=$cart_key&action=inc' class='btn btn-outline-secondary'>+</a>
                    </div>
                </td>
                <td>\$" . number_format($item_total, 2) . "</td>
                <td>
                    <a href='update_cart.php?id=$cart_key&action=delete' class='btn btn-danger btn-sm'>Delete</a>
                </td>
              </tr>";
    }
    echo "</tbody></table>";

    if (isset($_SESSION['applied_coupon'])) {
        $cp = $_SESSION['applied_coupon'];
        if ($cp['discount_type'] == 'percentage') {
            $discount_amount = ($subtotal_sum * $cp['value']) / 100;
        } else {
            $discount_amount = $cp['value'];
        }
    }
    $final_total = $subtotal_sum - $discount_amount;

    echo '<div class="row mt-4">
            <div class="col-md-5">
                <form action="cart.php" method="POST" class="d-flex gap-2">
                    <input type="text" name="coupon_code" class="form-control" placeholder="Coupon Code" value="'.(isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon']['code'] : '').'">
                    <button type="submit" name="apply_coupon" class="btn btn-dark">Apply</button>
                </form>
                '.$coupon_msg.'
            </div>
            
            <div class="col-md-7 text-end">
                <p class="mb-1">Subtotal: <strong>$'.number_format($subtotal_sum, 2).'</strong></p>';
                
                if($discount_amount > 0) {
                    echo '<p class="text-success mb-1">Discount ('.$_SESSION['applied_coupon']['code'].'): 
                          <strong>-$'.number_format($discount_amount, 2).'</strong> 
                          <a href="cart.php?remove_coupon=1" class="text-danger small ms-2">Remove</a></p>';
                }

    echo '      <h3 class="mt-2 text-primary">Grand Total: $'.number_format($final_total, 2).'</h3>
                <div class="mt-4">';
                    
                    // Show warning if checkout is blocked
                    if (!$can_proceed) {
                        echo '<div class="alert alert-warning d-inline-block py-2 px-3 small me-2">
                                <i class="bi bi-info-circle"></i> Please resolve stock issues to proceed.
                              </div>';
                    }

    echo '          <a href="product.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="checkout.php" class="btn btn-primary btn-lg ' . (!$can_proceed ? 'disabled' : '') . '">Proceed to Checkout</a>
                </div>
            </div>
          </div>';

} else {
    echo "<p class='alert alert-info'>Your cart is empty! <a href='product.php'>Go Shop</a></p>";
}
echo '</div>';

require_once __DIR__ . '/includes/footer.php';
?>