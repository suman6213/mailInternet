<?php include 'includes/header.php'; ?>
<?php
// Get user data from functions
$user = findUserById($_SESSION['user_id']);

// Check if user data was found. If not, redirect to login with an error message.
if (!$user) {
    $_SESSION['error'] = 'User not found. Please log in again.';
    header('Location: index.php?page=login');
    exit;
}

// Get the user's profile update requests
$profileUpdateRequests = getUserProfileUpdateRequests($_SESSION['user_id']);
?>
<style>
    /* Add styling for the search results list */
    .search-container {
        position: relative;
    }
    .search-results-list {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ccc;
        background-color: #fff;
        list-style: none;
        padding: 0;
        margin-top: 5px;
        z-index: 10;
        display: none; /* Initially hidden */
    }
    .search-results-list li {
        padding: 10px;
        cursor: pointer;
    }
    .search-results-list li:hover {
        background-color: #f0f0f0;
    }
    
    /* Corrected SweetAlert2 Input Fix */
    .swal2-container.swal2-password-modal .swal2-input {
        width: calc(100% - 20px) !important; /* Adjust width to fit within modal, accounting for padding/margin */
        margin-left: 10px;
        margin-right: 10px;
        box-sizing: border-box;
    }
</style>
<h2>Welcome, <?= htmlspecialchars($user['username']); ?>!</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card-grid">
    <div class="card">
        <h3>Your Current Balance</h3>
        <p class="balance-amount">$<?= number_format($user['balance'], 2); ?></p>
        <a href="index.php?page=transaction_history" class="btn btn-secondary">View Transaction History</a>
    </div>
    <div class="card">
        <h3>Transfer Money</h3>
        <form id="transfer-form" action="index.php?page=user_dashboard&action=transfer" method="POST" class="transfer-form">
            <div class="form-group">
                <label for="receiver_username">Receiver Username:</label>
                <div class="search-container">
                    <input type="text" id="receiver_username" name="receiver_username" placeholder="Search for a user..." required autocomplete="off">
                    <ul id="user-results" class="search-results-list"></ul>
                </div>
            </div>
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>
            </div>
            <button type="submit" class="btn">Transfer</button>
        </form>
    </div>
    <div class="card">
        <h3>Request Profile Update</h3>
        <form action="index.php?page=user_dashboard&action=requestProfileUpdate" method="POST" class="profile-update-form">
            <p>You can request to change your username or password. An admin will need to approve the change.</p>
            <div class="form-group">
                <label for="new_username">New Username:</label>
                <input type="text" id="new_username" name="new_username" placeholder="Leave blank to not change">
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" placeholder="Leave blank to not change">
            </div>
            <button type="submit" class="btn">Submit Request</button>
        </form>
    </div>
    <div class="card">
        <h3>Profile Update Requests</h3>
        <?php if (empty($profileUpdateRequests)): ?>
            <p>You have no profile update requests.</p>
        <?php else: ?>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Requested On</th>
                        <th>New Username</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($profileUpdateRequests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['requested_at']); ?></td>
                            <td><?= htmlspecialchars($request['new_username'] ?? 'N/A'); ?></td>
                            <td><span class="status-<?= htmlspecialchars($request['status']); ?>"><?= htmlspecialchars(ucfirst($request['status'])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Check for alerts from the server-side redirect
    <?php if (isset($_SESSION['success_alert'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= addslashes($_SESSION['success_alert']); ?>'
        });
        <?php unset($_SESSION['success_alert']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_alert'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= addslashes($_SESSION['error_alert']); ?>'
        });
        <?php unset($_SESSION['error_alert']); ?>
    <?php endif; ?>

    const form = document.getElementById('transfer-form');
    const senderUsername = '<?= htmlspecialchars($_SESSION['username']); ?>';

    // Live Search Logic
    const receiverInput = document.getElementById('receiver_username');
    const userResultsList = document.getElementById('user-results');
    let timeoutId;

    receiverInput.addEventListener('input', () => {
        clearTimeout(timeoutId);
        const keyword = receiverInput.value.trim();

        if (keyword.length > 0) {
            timeoutId = setTimeout(async () => {
                const response = await fetch(`index.php?is_ajax=1&keyword=${encodeURIComponent(keyword)}`);
                const users = await response.json();
                
                userResultsList.innerHTML = '';
                if (users.length > 0) {
                    users.forEach(user => {
                        const li = document.createElement('li');
                        li.textContent = user.username;
                        li.dataset.username = user.username;
                        li.addEventListener('click', () => {
                            receiverInput.value = user.username;
                            userResultsList.style.display = 'none';
                        });
                        userResultsList.appendChild(li);
                    });
                    userResultsList.style.display = 'block';
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'No users found.';
                    li.classList.add('no-results');
                    userResultsList.appendChild(li);
                    userResultsList.style.display = 'block';
                }
            }, 300); // 300ms delay to prevent excessive requests
        } else {
            userResultsList.innerHTML = '';
            userResultsList.style.display = 'none';
        }
    });

    // Hide results when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-container')) {
            userResultsList.style.display = 'none';
        }
    });

    // Handle form submission with password prompt
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const receiverUsername = receiverInput.value.trim();
        const amount = document.getElementById('amount').value;
        const parsedAmount = parseFloat(amount);

        // Simple client-side validation
        if (receiverUsername === '' || amount === '' || isNaN(parsedAmount) || parsedAmount <= 0) {
            Swal.fire('Error', 'Please enter a valid receiver username and a positive amount.', 'error');
            return;
        }

        if (receiverUsername === senderUsername) {
            Swal.fire('Error', 'You cannot send money to yourself.', 'error');
            return;
        }

        const { value: password } = await Swal.fire({
            title: 'Confirm Transfer',
            html: `
                <p>You are about to send **$${parsedAmount.toFixed(2)}** to **${receiverUsername}**.</p>
                <p>Please enter your password to confirm.</p>
            `,
            input: 'password', // Using native input property
            inputPlaceholder: 'Enter your password',
            customClass: {
                container: 'swal2-password-modal'
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm Transfer',
            showLoaderOnConfirm: true,
            focusConfirm: false,
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required.');
                }
                return password;
            },
            allowOutsideClick: () => !Swal.isLoading()
        });

        if (password) {
            // Create a hidden input field for the password and append it to the form
            const hiddenPasswordInput = document.createElement('input');
            hiddenPasswordInput.type = 'hidden';
            hiddenPasswordInput.name = 'password';
            hiddenPasswordInput.value = password;
            form.appendChild(hiddenPasswordInput);
            
            // Now submit the form with the password included
            form.submit();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>