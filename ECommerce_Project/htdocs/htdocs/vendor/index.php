<?php
// We use a 301 (Permanent) or 302 (Found) redirect to send users to the login page.
// Since index.php has no HTML content, this will happen instantly.

header("Location: login.php");
exit();
?>