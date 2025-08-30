<?php
include_once ('./includes/functions.php');
include_once ('config.php');
include_once('./includes/header.php');

checkAuth(false);

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Mark all notifications as read
markNotificationsAsRead($_SESSION['user_id']);

// Get all notifications for this user
$notifications = getNotifications($_SESSION['user_id']);
?>

<h2>Your Notifications</h2>

<?php if (empty($notifications)): ?>
    <p>No notifications yet.</p>
<?php else: ?>
    <ul class="notification-list">
        <?php foreach ($notifications as $notif): ?>
            <li class="<?= $notif['is_read'] == 0 ? 'unread' : 'read'; ?>">
                <span class="message"><?= htmlspecialchars($notif['message']); ?></span>
                <span class="date"><?= date('M d, Y H:i', strtotime($notif['created_at'])); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<style>
.notification-list {
    list-style: none;
    padding: 0;
}
.notification-list li {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}
.notification-list li.unread {
    background-color: #f0f8ff; /* Light blue for unread */
    font-weight: bold;
}
.notification-list li.read {
    background-color: #fff;
}
.notification-list .message {
    display: block;
}
.notification-list .date {
    display: block;
    font-size: 0.85em;
    color: #666;
}
</style>

