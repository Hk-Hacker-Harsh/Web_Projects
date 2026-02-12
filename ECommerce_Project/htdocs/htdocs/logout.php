<?php
    require_once __DIR__ . '/includes/session.php';
	session_unset();
	session_destroy();
	header("location:index.php");
?>