<?php
session_start();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'inc') {
        $_SESSION['cart'][$id]++;
    } 
    elseif ($action == 'dec') {
        if ($_SESSION['cart'][$id] > 1) {
            $_SESSION['cart'][$id]--;
        } else {
            unset($_SESSION['cart'][$id]); // Remove if it hits zero
        }
    } 
    elseif ($action == 'delete') {
        unset($_SESSION['cart'][$id]);
    }
}

header("Location: cart.php");
exit();
?>