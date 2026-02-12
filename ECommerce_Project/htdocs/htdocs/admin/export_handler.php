<?php
// 1. Clear any buffers BEFORE including anything
while (ob_get_level()) {
    ob_end_clean();
}

// 2. Include ONLY the database connection
// Using __DIR__ ensures we find the right file regardless of where we are
require_once __DIR__ . '/../db.php';

// 3. Simple Security Check (Direct Session Access)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized Access");
}

if (isset($_GET['table'])) {
    $table = mysqli_real_escape_string($conn, $_GET['table']);
    $allowed = ['users', 'products', 'categories', 'vendors', 'orders', 'reviews', 'wishlist', 'banners'];
    
    if (in_array($table, $allowed)) {
        
        // 4. Force CSV Headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="HK_Export_'.$table.'_'.date('Y-m-d').'.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // 5. Fetch data
        $res = mysqli_query($conn, "SELECT * FROM `$table` ");
        
        if ($res) {
            $header_sent = false;
            while ($row = mysqli_fetch_assoc($res)) {
                if (!$header_sent) {
                    fputcsv($output, array_keys($row));
                    $header_sent = true;
                }
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        // 6. TERMINATE IMMEDIATELY
        // This prevents the script from running twice or adding extra data
        exit(); 
    }
}