<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';
protect_page();

if (empty($_SESSION['cart'])) {
    header("Location: product.php");
    exit();
}

// Fetch items for summary
$ids = implode(',', array_keys($_SESSION['cart']));
$query = "SELECT * FROM products WHERE id IN ($ids)";
$result = mysqli_query($conn, $query);

$subtotal_sum = 0;
$discount_amount = 0;
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Final Checkout</h4>
                </div>
                <div class="card-body">
                    <h5>Order Summary</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): 
                                $qty = $_SESSION['cart'][$row['id']];
                                $item_total = $row['price'] * $qty;
                                $subtotal_sum += $item_total;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo $qty; ?></td>
                                <td>$<?php echo number_format($item_total, 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <tr class="table-light">
                                <td colspan="2">Subtotal</td>
                                <td>$<?php echo number_format($subtotal_sum, 2); ?></td>
                            </tr>

                            <?php 
                            if (isset($_SESSION['applied_coupon'])) {
                                $cp = $_SESSION['applied_coupon'];
                                if ($cp['discount_type'] == 'percentage') {
                                    $discount_amount = ($subtotal_sum * $cp['value']) / 100;
                                } else {
                                    $discount_amount = $cp['value'];
                                }
                                ?>
                                <tr class="text-success">
                                    <td colspan="2">Discount (<?php echo $cp['code']; ?>)</td>
                                    <td>-$<?php echo number_format($discount_amount, 2); ?></td>
                                </tr>
                                <?php
                            }
                            $final_payable = $subtotal_sum - $discount_amount;
                            ?>

                            <tr class="table-info">
                                <td colspan="2"><strong>Final Payable Amount</strong></td>
                                <td><strong>$<?php echo number_format($final_payable, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <form action="process_checkout.php" method="POST">
                        <input type="hidden" name="total_amount" value="<?php echo $final_payable; ?>">
                        
                        <?php if(isset($_SESSION['applied_coupon'])): ?>
                            <input type="hidden" name="coupon_id" value="<?php echo $_SESSION['applied_coupon']['id']; ?>">
                        <?php endif; ?>

                        <h5 class="mt-4 mb-3">Select Payment Method</h5>
                        <div class="card p-3 mb-4 bg-light border">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label fw-bold" for="cod">
                                    Cash on Delivery (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay">
                                <label class="form-check-label fw-bold" for="razorpay">
                                    Online Payment (Razorpay)
                                </label>
                                <div class="text-muted small">Pay securely via Cards, UPI, or Netbanking</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg">Place Order</button>
                            <a href="cart.php" class="btn btn-link text-muted">Back to Cart</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>