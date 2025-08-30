<?php
// Include necessary files
include 'includes/header.php';
include_once 'config.php';
include_once 'includes/functions.php';

// Ensure user is admin
checkAuth(true);

// Pagination
$transactionsPerPage = 4;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($currentPage - 1) * $transactionsPerPage;
$isTransactionsVisible = isset($_GET['p']);

// Fetch dashboard data
$users = searchUsers('');
$pendingRequests = getPendingProfileUpdateRequests();
$totalTransactions = countAllTransactions();
$totalPages = ceil($totalTransactions / $transactionsPerPage);
$transactions = getAllTransactionsWithPagination($transactionsPerPage, $offset);
?>

<style>
    body {
    margin: 0;
    padding: 0;
    background-color: #f3f4f6; /* optional light background */
    font-family: Arial, sans-serif;
}


/* Dashboard container */
.dashboard-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem; /* space between columns */
    padding: 1rem; /* overall padding */
    width: 100%;
    box-sizing: border-box;
}

/* Sections */
.dashboard-section {
    background-color: #fff;
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.dashboard-section h2 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #333;
}

/* Tables */
.table-auto, .request-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.table-auto th, .table-auto td,
.request-table th, .request-table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

.table-auto th, .request-table th {
    background-color: #f7f7f7;
}

/* Buttons */
.btn {
    padding: 0.4rem 0.8rem;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    font-size: 0.9rem;
    border: none;
}

.btn-primary { background-color: #4f46e5; color: #fff; }
.btn-primary:hover { background-color: #4338ca; }

.btn-delete { background-color: #ef4444; color: #fff; }
.btn-delete:hover { background-color: #dc2626; }

.btn-success { background-color: #10b981; color: #fff; }
.btn-success:hover { background-color: #059669; }

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-content {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    max-width: 400px;
    text-align: center;
}
.modal-actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
}

/* Flex container for Accept/Reject buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Form */
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.3rem;
}
.form-input {
    width: 100%;
    padding: 0.5rem;
    border-radius: 5px;
    border: 1px solid #ccc;
}

/* Pagination */
.pagination {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}
.pagination a {
    padding: 0.5rem 0.8rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
}
.pagination a.active {
    background-color: #6366f1;
    color: #fff;
    border-color: #6366f1;
}

/* Transaction history hidden by default */
.transaction-history-container { display: none; }

</style>

<div class="dashboard-container">
    <!-- User Management -->
    <div class="dashboard-section">
        <h2>User Management</h2>
        <table class="table-auto">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']); ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <td>
                            <button onclick="showDeleteModal(<?= $user['id']; ?>)" class="btn btn-delete">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Create User/Admin -->
    <div class="dashboard-section">
        <h2>Create New User/Admin</h2>
        <form action="index.php?page=admin_dashboard&action=createUser" method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <div class="form-group" style="display: flex; align-items: center;">
                <input type="checkbox" id="is_admin" name="is_admin">
                <label for="is_admin" style="margin-left: 0.5rem;">Create as Admin?</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create User</button>
        </form>
    </div>
</div>

<div class="dashboard-container" style="margin-top:2rem;">
    <!-- Transaction History -->
    <div class="dashboard-section">
        <h2 id="transactions">Transaction History</h2>
        <button id="toggle-transactions-btn" class="btn btn-primary" style="margin-bottom:1rem;">
            <?= $isTransactionsVisible ? 'Hide Transaction History' : 'Show Transaction History'; ?>
        </button>
        <div id="transaction-history-container" class="transaction-history-container" style="display: <?= $isTransactionsVisible ? 'block' : 'none'; ?>;">
            <?php if (empty($transactions)): ?>
                <p>No transactions found.</p>
            <?php else: ?>
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Receiver</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= htmlspecialchars($transaction['sender_username'] ?? 'N/A'); ?></td>
                                <td><?= htmlspecialchars($transaction['receiver_username'] ?? 'N/A'); ?></td>
                                <td>$<?= number_format($transaction['amount'], 2); ?></td>
                                <td><?= htmlspecialchars($transaction['transaction_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <?php for ($i=1; $i<=$totalPages; $i++): ?>
                        <a href="index.php?page=admin_dashboard&p=<?= $i; ?>#transactions" class="<?= ($i==$currentPage)?'active':''; ?>"><?= $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="dashboard-section">
        <h2>Pending Profile Update Requests</h2>
        <div class="card">
            <?php if(empty($pendingRequests)): ?>
                <p>No pending profile update requests.</p>
            <?php else: ?>
                <table class="request-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>New Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingRequests as $request): ?>
                            <tr>
                                <td><?= htmlspecialchars($request['username']); ?></td>
                                <td><?= htmlspecialchars($request['new_username'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="index.php?page=admin_dashboard&action=acceptProfileUpdateRequest&id=<?= $request['id']; ?>" class="btn btn-success">Accept</a>
                                        <a href="index.php?page=admin_dashboard&action=rejectProfileUpdateRequest&id=<?= $request['id']; ?>" class="btn btn-delete">Reject</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete this user?</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="hideDeleteModal()">No</button>
            <a id="confirm-delete-btn" href="#" class="btn btn-delete">Yes, Delete</a>
        </div>
    </div>
</div>

<script>
// Delete modal
function showDeleteModal(userId){
    const modal = document.getElementById('delete-modal');
    const confirmBtn = document.getElementById('confirm-delete-btn');
    confirmBtn.href = `index.php?page=admin_dashboard&action=delete&id=${userId}`;
    modal.style.display = 'flex';
}
function hideDeleteModal(){ document.getElementById('delete-modal').style.display='none'; }

// Toggle transactions
document.getElementById('toggle-transactions-btn').addEventListener('click', function(){
    const container = document.getElementById('transaction-history-container');
    if(container.style.display==='none'||container.style.display===''){
        container.style.display='block'; this.textContent='Hide Transaction History';
    } else { container.style.display='none'; this.textContent='Show Transaction History'; }
});
</script>

<?php include 'includes/footer.php'; ?>
