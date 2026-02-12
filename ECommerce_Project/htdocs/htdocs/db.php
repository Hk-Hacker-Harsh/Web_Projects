<?php
require_once __DIR__ . '/config.php';

// 1. Tell MySQLi to throw Exceptions instead of just returning false
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($conn, "utf8mb4");
} catch (Exception $e) {
    // 2. If the connection itself fails, handle it immediately
    handle_global_exception($e);
}

// 3. Global Function to handle ANY error in the application
function handle_global_exception($e) {
    // Log the actual technical error to a private file
    $log_message = "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
    error_log($log_message, 3, __DIR__ . '/private_error_log.log');

    // Clear any half-rendered HTML and show the friendly error page
    if (ob_get_length()) ob_clean();
    include __DIR__ . '/error.php';
    exit();
}

// Set this function as the "catcher" for the whole site
set_exception_handler('handle_global_exception');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>