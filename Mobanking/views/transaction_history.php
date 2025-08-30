<?php include 'includes/header.php'; ?>
<?php
// Get and sort transactions based on URL parameters
$transactions = getTransactionsForUser($_SESSION['user_id']);

$sortBy = $_GET['sort_by'] ?? 'transaction_date';
$sortOrder = $_GET['sort_order'] ?? 'desc';

if ($sortBy === 'amount' || $sortBy === 'transaction_date') {
    $transactions = BubbleSortTransactions($transactions, $sortBy, $sortOrder);
}
?>
<h2>Transaction History</h2>
<div class="card">
    <div class="sort-options">
        <span>Sort by:</span>
        <a href="index.php?page=transaction_history&sort_by=transaction_date&sort_order=desc" class="sort-link">Date (Newest)</a> |
        <a href="index.php?page=transaction_history&sort_by=transaction_date&sort_order=asc" class="sort-link">Date (Oldest)</a> |
        <a href="index.php?page=transaction_history&sort_by=amount&sort_order=desc" class="sort-link">Amount (High to Low)</a> |
        <a href="index.php?page=transaction_history&sort_by=amount&sort_order=asc" class="sort-link">Amount (Low to High)</a>
    </div>
    <table class="transaction-table">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No transactions found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['sender_username'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($transaction['receiver_username'] ?? 'N/A'); ?></td>
                        <td class="<?= $transaction['sender_id'] === $_SESSION['user_id'] ? 'outgoing' : 'incoming'; ?>">
                            $<?= number_format($transaction['amount'], 2); ?>
                        </td>
                        <td><?= htmlspecialchars($transaction['transaction_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include 'includes/footer.php'; ?>
