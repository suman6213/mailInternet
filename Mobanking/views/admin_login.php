<?php include 'includes/header.php'; ?>
<h2>Admin Login</h2>
<form action="index.php?page=admin_login" method="POST" class="form-card">
    <div class="form-group">
        <label for="username">Admin Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Admin Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn">Login as Admin</button>
</form>
<?php include 'includes/footer.php'; ?>
