<?php
// 1. SYSTEM LOGIC: MUST BE AT THE VERY TOP
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

// 2. AUTHENTICATION CHECK: Must happen before header.php
// If the user isn't logged in, protect_page() will redirect them.
// Without ob_start, this redirect ONLY works if called before header.php.
protect_page(); 

$user_id = $_SESSION['user_id'];

// 3. DATA FETCHING
// Fetch User Details
$user_query = "SELECT name, email, created_at FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Fetch Order History
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);

// 4. DISPLAY LOGIC: START SENDING HTML NOW
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_data['name']); ?>&background=random" class="rounded-circle" alt="User Avatar" width="100">
                    </div>
                    <h4 class="card-title"><?php echo htmlspecialchars($user_data['name']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p class="small">Member since: <?php echo date('M Y', strtotime($user_data['created_at'])); ?></p>
                    <hr>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">Sign Out</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h3 class="mb-4">Order History</h3>
            
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <?php 
                                            $status_class = match($order['status']) {
                                                'paid' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'shipped' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-light border text-center py-5">
                    <p class="mb-3">You haven't placed any orders yet.</p>
                    <a href="product.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>