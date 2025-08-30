<?php include 'includes/header.php'; ?>
<h2>Login</h2>
<form action="index.php?page=login" method="POST" class="form-card">
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn">Login</button>
    <div class="form-link">
        Don't have an account? <a href="index.php?page=register">Register here</a>
    </div>
</form>
<?php include 'includes/footer.php'; ?>
