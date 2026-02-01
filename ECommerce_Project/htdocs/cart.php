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
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM products WHERE id IN ($ids)";
    $result = mysqli_query($conn, $query);

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
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $qty = $_SESSION['cart'][$id];
        $item_total = $row['price'] * $qty;
        $subtotal_sum += $item_total;

        echo "<tr>
                <td>{$row['name']}</td>
                <td>\${$row['price']}</td>
                <td>
                    <div class='btn-group btn-group-sm'>
                        <a href='update_cart.php?id=$id&action=dec' class='btn btn-outline-secondary'>-</a>
                        <span class='btn border-secondary disabled'>$qty</span>
                        <a href='update_cart.php?id=$id&action=inc' class='btn btn-outline-secondary'>+</a>
                    </div>
                </td>
                <td>\${$item_total}</td>
                <td>
                    <a href='update_cart.php?id=$id&action=delete' class='btn btn-danger btn-sm'>Delete</a>
                </td>
              </tr>";
    }
    echo "</tbody></table>";

    // --- Start Calculation Section ---
    if (isset($_SESSION['applied_coupon'])) {
        $cp = $_SESSION['applied_coupon'];
        if ($cp['discount_type'] == 'percentage') {
            $discount_amount = ($subtotal_sum * $cp['value']) / 100;
        } else {
            $discount_amount = $cp['value'];
        }
    }
    $final_total = $subtotal_sum - $discount_amount;
    // --- End Calculation Section ---

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
                <div class="mt-4">
                    <a href="product.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                </div>
            </div>
          </div>';

} else {
    echo "<p class='alert alert-info'>Your cart is empty! <a href='product.php'>Go Shop</a></p>";
}
echo '</div>';

require_once __DIR__ . '/includes/footer.php';
?>