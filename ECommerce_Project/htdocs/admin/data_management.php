<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/session.php'; 
require_once __DIR__ . '/admin_auth.php'; 

// --- DYNAMIC IMPORT LOGIC ---
$message = "";
if (isset($_POST['import_type']) && isset($_FILES['csv_file'])) {
    $table = mysqli_real_escape_string($conn, $_POST['import_type']);
    $file = $_FILES['csv_file']['tmp_name'];
    
    if (file_exists($file) && $_FILES['csv_file']['size'] > 0) {
        $handle = fopen($file, "r");
        $csv_headers = fgetcsv($handle); 
        
        $success = 0; $errors = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (empty(array_filter($data))) continue;

            $columns = []; $values = []; $updates = [];
            foreach ($csv_headers as $index => $col_name) {
                if (isset($data[$index])) {
                    $val = mysqli_real_escape_string($conn, $data[$index]);
                    $columns[] = "`$col_name`";
                    $values[] = "'$val'";
                    // Update all fields except ID if record already exists
                    if ($col_name != 'id') { 
                        $updates[] = "`$col_name` = '$val'"; 
                    }
                }
            }

            $q = "INSERT INTO `$table` (".implode(', ', $columns).") 
                  VALUES (".implode(', ', $values).") 
                  ON DUPLICATE KEY UPDATE ".implode(', ', $updates);

            if (mysqli_query($conn, $q)) $success++; else $errors++;
        }
        fclose($handle);
        $message = "<div class='alert alert-success shadow-sm'>
                        <i class='bi bi-check-circle-fill me-2'></i>
                        <b>$table Sync Complete:</b> Success: $success | Failed: $errors
                    </div>";
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_nav.php';
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Data Management Hub</h2>
        <span class="badge bg-primary px-3 py-2">System Version 2.0</span>
    </div>

    <?= $message; ?>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-download me-2"></i>Export Center</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small">Select any database table to generate a clean backup CSV.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Available Tables</label>
                        <select id="export_table_select" class="form-select form-select-lg border-2">
                            <optgroup label="Core Data">
                                <option value="users">User Accounts</option>
                                <option value="products">Product Catalog</option>
                                <option value="categories">Categories</option>
                                <option value="vendors">Vendors</option>
                            </optgroup>
                            <optgroup label="Transactions & Interaction">
                                <option value="orders">Orders History</option>
                                <option value="reviews">Customer Reviews</option>
                                <option value="wishlist">User Wishlists</option>
                            </optgroup>
                            <optgroup label="Site Settings">
                                <option value="banners">Homepage Banners</option>
                                <option value="seo_settings">SEO Meta Data</option>
                            </optgroup>
                        </select>
                    </div>
                    <button type="button" onclick="startExport()" class="btn btn-primary w-100 btn-lg shadow-sm">
                        <i class="bi bi-file-earmark-arrow-down-fill me-2"></i>Download CSV
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-light h-100 border-start border-success border-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Smart Sync (Import)</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Target Destination</label>
                            <select name="import_type" class="form-select border-2">
                                <option value="products">Products</option>
                                <option value="users">Users</option>
                                <option value="categories">Categories</option>
                                <option value="vendors">Vendors</option>
                                <option value="orders">Orders (Caution!)</option>
                                <option value="banners">Banners</option>
                                <option value="seo_settings">SEO Settings</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">CSV Source File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                            <div class="form-text mt-2 small">
                                <i class="bi bi-info-circle"></i> Headers must match the database column names.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-lg shadow-sm">
                            <i class="bi bi-arrow-repeat me-2"></i>Execute Sync
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startExport() {
    const table = document.getElementById('export_table_select').value;
    // Calling the clean external handler
    window.location.href = 'export_handler.php?table=' + table;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>