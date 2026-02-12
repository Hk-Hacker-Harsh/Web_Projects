<?php
ob_start();
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

if (empty($_SESSION['cart'])) {
    header("Location: product.php");
    exit();
}

$subtotal_sum = 0;
$discount_amount = 0;
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-truck me-2"></i>Shipping Details</h4>
                </div>
                <div class="card-body">
                    <form action="process_checkout.php" method="POST" id="checkoutForm">
                        
                        <div class="mb-4">
                            <label for="shipping_address" class="form-label fw-bold">Full Delivery Address</label>
                            <textarea name="shipping_address" id="shipping_address" class="form-control" rows="4" 
                                placeholder="House No, Street Name, City, State, ZIP Code" required></textarea>
                            <div class="form-text">Please double-check your address before proceeding.</div>
                        </div>

                        <hr>

                        <h5 class="mt-4 mb-3">Select Payment Method</h5>
                        <div class="list-group mb-4">
                            <label class="list-group-item d-flex gap-3">
                                <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <span>
                                    <strong class="d-block">Cash on Delivery (COD)</strong>
                                    <small class="text-muted">Pay when you receive the package.</small>
                                </span>
                            </label>
                            <label class="list-group-item d-flex gap-3">
                                <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" id="stripe" value="stripe">
                                <span>
                                    <strong class="d-block">Online Payment (Stripe)</strong>
                                    <small class="text-muted">Pay securely with Credit/Debit Card.</small>
                                </span>
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Place Order
                            </button>
                            <a href="cart.php" class="btn btn-link text-muted btn-sm">Modify Cart</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <?php 
                            foreach ($_SESSION['cart'] as $cart_key => $qty): 
                                $parts = explode('_', $cart_key);
                                $p_id = (int)$parts[0];

                                $p_query = "SELECT name, price FROM products WHERE id = $p_id";
                                $p_res = mysqli_query($conn, $p_query);
                                $product = mysqli_fetch_assoc($p_res);

                                if (!$product) continue;

                                $unit_price = $product['price'];
                                $variation_text = "";

                                for ($i = 1; $i < count($parts); $i++) {
                                    $v_id = (int)$parts[$i];
                                    if ($v_id > 0) {
                                        $v_query = "SELECT variation_value, price_modifier FROM product_variations WHERE id = $v_id";
                                        $v_res = mysqli_query($conn, $v_query);
                                        if ($v_data = mysqli_fetch_assoc($v_res)) {
                                            $unit_price += $v_data['price_modifier'];
                                            $variation_text .= " <span class='badge bg-light text-dark border'>" . $v_data['variation_value'] . "</span>";
                                        }
                                    }
                                }

                                $item_total = $unit_price * $qty;
                                $subtotal_sum += $item_total;
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <small class="text-muted">Qty: <?php echo $qty; ?> x $<?php echo number_format($unit_price, 2); ?></small>
                                    <div><?php echo $variation_text; ?></div>
                                </td>
                                <td class="text-end">$<?php echo number_format($item_total, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal_sum, 2); ?></span>
                    </div>

                    <?php if (isset($_SESSION['applied_coupon'])): 
                        $cp = $_SESSION['applied_coupon'];
                        $discount_amount = ($cp['discount_type'] == 'percentage') ? ($subtotal_sum * $cp['value']) / 100 : $cp['value'];
                    ?>
                        <div class="d-flex justify-content-between text-success mb-2">
                            <span>Discount (<?php echo $cp['code']; ?>)</span>
                            <span>-$<?php echo number_format($discount_amount, 2); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php $final_payable = max(0, $subtotal_sum - $discount_amount); ?>

                    <div class="d-flex justify-content-between mt-3 border-top pt-3">
                        <h4 class="fw-bold">Total</h4>
                        <h4 class="fw-bold text-primary">$<?php echo number_format($final_payable, 2); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>