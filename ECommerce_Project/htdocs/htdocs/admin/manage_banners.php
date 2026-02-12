<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
require_once __DIR__ . '/admin_auth.php';

$message = "";

// 1. Handle Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_banner'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $target_dir = "../Assets/Banners/";
        $file_name = time() . "_" . basename($_FILES["banner_image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
            mysqli_query($conn, "INSERT INTO banners (image_name, title) VALUES ('$file_name', '$title')");
            $message = "<div class='alert alert-success'>Banner uploaded successfully!</div>";
        }
    }
}

// 2. Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $res = mysqli_query($conn, "SELECT image_name FROM banners WHERE id = $id");
    if ($b = mysqli_fetch_assoc($res)) {
        unlink("../Assets/Banners/" . $b['image_name']);
        mysqli_query($conn, "DELETE FROM banners WHERE id = $id");
        header("Location: manage_banners.php?msg=deleted");
        exit();
    }
}

$banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">Upload New Banner</div>
        <div class="card-body">
            <?= $message; ?>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="title" class="form-control" placeholder="Banner Title (Optional)">
                </div>
                <div class="col-md-4">
                    <input type="file" name="banner_image" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="upload_banner" class="btn btn-success w-100">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-dark text-white">Current Banners</div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($banners)): ?>
                    <tr>
                        <td><img src="../Assets/Banners/<?= $row['image_name']; ?>" style="height: 60px; width: 150px; object-fit: cover;" class="rounded"></td>
                        <td><?= $row['title']; ?></td>
                        <td>
                            <a href="?delete=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this banner?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>