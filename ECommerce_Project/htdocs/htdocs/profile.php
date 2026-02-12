<?php
// 1. SYSTEM LOGIC
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session.php';

protect_page(); 
$user_id = $_SESSION['user_id'];
$message = "";

// 2. PROFILE IMAGE UPLOAD LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar'])) {
    $target_dir = "Assets/avatars/";
    
    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
    $new_file_name = "user_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_file_name;

    // Basic validation
    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            // Update database
            $update_img = "UPDATE users SET avatar = '$new_file_name' WHERE id = $user_id";
            if (mysqli_query($conn, $update_img)) {
                $message = "<div class='alert alert-success'>Avatar updated!</div>";
            }
        }
    }
}

// 3. DATA FETCHING
$user_query = "SELECT name, email, avatar, created_at FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Order History & Wishlist queries remain same
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);

$wishlist_query = "SELECT p.* FROM wishlist w 
                   JOIN products p ON w.product_id = p.id 
                   WHERE w.user_id = $user_id 
                   ORDER BY w.created_at DESC";
$wishlist_result = mysqli_query($conn, $wishlist_query);

require_once __DIR__ . '/includes/header.php'; 
?>

<style>
    .avatar-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    .avatar-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }
</style>

<div class="container mt-5">
    <?= $message; ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body text-center">
                    
                    <div class="mb-3">
                        <form id="avatarForm" method="POST" enctype="multipart/form-data">
                            <label for="avatarInput" class="avatar-wrapper">
                                <?php 
                                    $img_src = !empty($user_data['avatar']) 
                                        ? "Assets/avatars/" . $user_data['avatar'] 
                                        : "https://ui-avatars.com/api/?name=" . urlencode($user_data['name']) . "&background=random&size=128";
                                ?>
                                <img src="<?= $img_src; ?>" class="rounded-circle shadow-sm" alt="User Avatar" width="120" height="120" style="object-fit: cover;">
                                <div class="avatar-overlay">
                                    <i class="bi bi-camera-fill h4"></i>
                                </div>
                                <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
                            </label>
                        </form>
                    </div>

                    <h4 class="card-title fw-bold"><?php echo htmlspecialchars($user_data['name']); ?></h4>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p class="small text-secondary">Member since: <?php echo date('M Y', strtotime($user_data['created_at'])); ?></p>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="wishlist.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-heart me-1"></i> Full Wishlist</a>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm">Sign Out</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <ul class="nav nav-pills mb-4 shadow-sm p-2 bg-white rounded" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#orders" type="button">Order History</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#wishlist" type="button">My Wishlist</button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">
                <div class="tab-pane fade show active" id="orders">
                    <h5 class="fw-bold mb-3">Recent Orders</h5>
                    <?php if (mysqli_num_rows($orders_result) > 0): ?>
                        <div class="table-responsive bg-white rounded shadow-sm">
                            <table class="table table-hover align-middle mb-0 border">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                        <tr>
                                            <td class="fw-bold">#<?= $order['id']; ?></td>
                                            <td><?= date('d M Y', strtotime($order['created_at'])); ?></td>
                                            <td>$<?= number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <?php $sc = match($order['status']) {'paid'=>'bg-success','pending'=>'bg-warning text-dark','shipped'=>'bg-info',default=>'bg-secondary'}; ?>
                                                <span class="badge <?= $sc; ?>"><?= ucfirst($order['status']); ?></span>
                                            </td>
                                            <td><a href="order_details.php?id=<?= $order['id']; ?>" class="btn btn-sm btn-primary">View</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border text-center py-4">No orders yet.</div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="wishlist">
                    <h5 class="fw-bold mb-3">Saved Items</h5>
                    <div class="row g-3">
                        <?php if (mysqli_num_rows($wishlist_result) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($wishlist_result)): ?>
                                <div class="col-sm-6">
                                    <div class="card h-100 border-0 shadow-sm d-flex flex-row align-items-center p-2">
                                        <img src="Assets/upload/<?= $item['image']; ?>" class="rounded" width="70" height="70" style="object-fit: cover;">
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-0 text-truncate" style="max-width: 150px;"><?= htmlspecialchars($item['name']); ?></h6>
                                            <span class="text-primary small fw-bold">$<?= number_format($item['price'], 2); ?></span>
                                        </div>
                                        <div class="pe-2">
                                            <a href="product_details.php?id=<?= $item['id']; ?>" class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12"><div class="alert alert-light border text-center py-4">Wishlist empty.</div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>