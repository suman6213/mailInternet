<?php
// This is the common header for all pages.
// Check if user is logged in to get unread notification count
$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $unreadCount = getUnreadNotificationCount($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Banking System</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="assets/script.js" defer></script>
</head>
<body>
    <header class="main-header">
        <div class="logo">Easy Pay</div>
        <nav>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['is_admin']): ?>
                    <a href="index.php?page=admin_dashboard" class="nav-link">Admin Dashboard</a>
                <?php else: ?>
                    <a href="index.php?page=user_dashboard" class="nav-link">Dashboard</a>
                    <a href="index.php?page=transaction_history" class="nav-link">History</a>
                    <a href="index.php?page=notifications" class="nav-link notification-link">
                        <i class="fa-solid fa-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-badge"><?= $unreadCount; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="index.php?page=logout" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="index.php?page=login" class="nav-link">Login</a>
                <a href="index.php?page=register" class="nav-link">Register</a>
                <a href="index.php?page=admin_login" class="nav-link">Admin Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <div class="container">
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>