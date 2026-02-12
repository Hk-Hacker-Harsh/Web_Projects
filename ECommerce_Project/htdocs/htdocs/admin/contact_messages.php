<?php
// 1. SYSTEM LOGIC: SESSION AND AUTH FIRST
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/admin_auth.php'; // Ensures only admin can access
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';

$message_status = "";

// 2. HANDLE DELETE ACTION
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_query = "DELETE FROM contact_messages WHERE id = $delete_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $message_status = "<div class='alert alert-success'>Message deleted successfully.</div>";
    } else {
        $message_status = "<div class='alert alert-danger'>Error deleting message: " . mysqli_error($conn) . "</div>";
    }
}

// 3. FETCH ALL MESSAGES
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid mt-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Contact Inquiries</h2>
        <span class="badge bg-primary px-3 py-2"><?= mysqli_num_rows($result); ?> Total Messages</span>
    </div>

    <?= $message_status; ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Sender Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message Preview</th>
                        <th>Received On</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?= $row['id']; ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></div>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($row['email']); ?>" class="text-decoration-none small">
                                        <?= htmlspecialchars($row['email']); ?>
                                    </a>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['subject']); ?></span></td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="<?= htmlspecialchars($row['message']); ?>">
                                        <?= htmlspecialchars($row['message']); ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted"><?= date('d M Y, h:i A', strtotime($row['created_at'])); ?></small>
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group">
                                        <a href="mailto:<?= htmlspecialchars($row['email']); ?>?subject=Re: <?= htmlspecialchars($row['subject']); ?>" 
                                           class="btn btn-sm btn-outline-primary">Reply</a>
                                        
                                        <a href="contact_messages.php?delete_id=<?= $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted italic">No contact messages found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Styling to match your Admin Dashboard theme */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    .text-truncate {
        cursor: help;
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>