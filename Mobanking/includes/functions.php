<?php

// ---------------- Authentication ----------------
function checkAuth($isAdminRequired)
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
    if ($isAdminRequired && !isAdmin($_SESSION['user_id'])) {
        header('Location: index.php?page=user_dashboard');
        exit;
    }
}

function isAdmin($userId)
{
    global $conn;
    $userId = (int)$userId;
    $sql = "SELECT id FROM admins WHERE id = $userId";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}

// ---------------- Login / Register ----------------
function loginUser($username, $password)
{
    global $conn;
    $username = mysqli_real_escape_string($conn, trim($username));
    $sql = "SELECT id, username, password, balance FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = false;
        $_SESSION['success_alert'] = 'Login successful!';
        header('Location: index.php?page=user_dashboard');
        exit;
    } else {
        $_SESSION['error_alert'] = 'Invalid username or password.';
        header('Location: index.php?page=login');
        exit;
    }
}

function adminLoginUser($username, $password)
{
    global $conn;
    $username = mysqli_real_escape_string($conn, trim($username));
    $sql = "SELECT id, username, password FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['is_admin'] = true;
        $_SESSION['success_alert'] = 'Admin login successful!';
        header('Location: index.php?page=admin_dashboard');
        exit;
    } else {
        $_SESSION['error_alert'] = 'Invalid admin username or password.';
        header('Location: index.php?page=admin_login');
        exit;
    }
}

function registerUser($username, $password)
{
    global $conn;
    $username = mysqli_real_escape_string($conn, trim($username));

    $sql_check = "SELECT username FROM users WHERE username = '$username' 
                  UNION ALL 
                  SELECT username FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error_alert'] = 'Username already exists.';
        header('Location: index.php?page=register');
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_alert'] = 'Registration successful! Please log in.';
        header('Location: index.php?page=login');
        exit;
    } else {
        $_SESSION['error_alert'] = 'Registration failed.';
        header('Location: index.php?page=register');
        exit;
    }
}

function logoutUser()
{
    session_start();
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// ---------------- Admin User Management ----------------
function createUser($username, $password, $isAdmin)
{
    global $conn;
    checkAuth(true);
    $username = mysqli_real_escape_string($conn, trim($username));

    $sql_check = "SELECT username FROM users WHERE username = '$username' 
                  UNION ALL 
                  SELECT username FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error_alert'] = 'Username already exists.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if ($isAdmin) {
            $sql = "INSERT INTO admins (username, password) VALUES ('$username', '$hashed_password')";
        } else {
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        }
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_alert'] = 'User created successfully!';
        } else {
            $_SESSION['error_alert'] = 'Failed to create user.';
        }
    }
    header('Location: index.php?page=admin_dashboard');
    exit;
}

function deleteUser($userId)
{
    global $conn;
    checkAuth(true);
    $userId = (int)$userId;

    $sql = "SELECT id FROM users WHERE id = $userId";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $sql_delete = "DELETE FROM users WHERE id = $userId";
        if (mysqli_query($conn, $sql_delete)) {
            $_SESSION['success_alert'] = 'User deleted successfully.';
        } else {
            $_SESSION['error_alert'] = 'Failed to delete user.';
        }
    } else {
        $_SESSION['error_alert'] = 'User not found or is an admin.';
    }
    header('Location: index.php?page=admin_dashboard');
    exit;
}

function getAllUsers()
{
    global $conn;
    $sql = "SELECT id, username, balance FROM users";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function searchUsers($keyword)
{
    global $conn;
    $keyword = mysqli_real_escape_string($conn, trim($keyword));

    $sql = "SELECT id, username, balance 
            FROM users 
            WHERE username LIKE '%$keyword%' 
            ORDER BY username ASC";

    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function findUserById($id)
{
    global $conn;
    $id = (int)$id;
    // Corrected SQL query to include the 'password' field for users
    $sql = "(SELECT id, username, password, balance, 0 AS is_admin FROM users WHERE id = $id)
            UNION
            (SELECT id, username, password, NULL AS balance, 1 AS is_admin FROM admins WHERE id = $id)";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// ---------------- Admin: Count All Transactions ----------------
function countAllTransactions()
{
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM transactions";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// ---------------- Admin: Get All Transactions with Pagination ----------------
function getAllTransactionsWithPagination($limit = 10, $offset = 0)
{
    global $conn;

    $limit = (int)$limit;
    $offset = (int)$offset;

    $sql = "SELECT t.*, 
                   s.username AS sender_username, 
                   r.username AS receiver_username
            FROM transactions t
            LEFT JOIN users s ON t.sender_id = s.id
            LEFT JOIN users r ON t.receiver_id = r.id
            ORDER BY t.transaction_date DESC
            LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// ---------------- Transactions ----------------
function transferMoney($senderId, $receiverUsername, $amount)
{
    global $conn;
    $senderId = (int)$senderId;
    $receiverUsername = mysqli_real_escape_string($conn, trim($receiverUsername));
    $amount = floatval($amount);
    $password = $_POST['password'] ?? '';

    // Step 1: Validate the amount first.
    if ($amount <= 0) {
        $_SESSION['error_alert'] = 'Invalid transfer amount.';
        header('Location: index.php?page=user_dashboard');
        exit;
    }

    // Step 2: Check if the receiver exists and is a regular user.
    $sql_receiver = "SELECT id, username FROM users WHERE username = '$receiverUsername'";
    $result_receiver = mysqli_query($conn, $sql_receiver);
    $receiver = mysqli_fetch_assoc($result_receiver);

    if (!$receiver) {
        $_SESSION['error_alert'] = 'Receiver not found.';
        header('Location: index.php?page=user_dashboard');
        exit;
    }

    // Step 3: Get sender information including their password.
    $sender = findUserById($senderId);

    // Step 4: Check if the sender is trying to send money to themselves.
    if ($sender['id'] === $receiver['id']) {
        $_SESSION['error_alert'] = 'You cannot send money to yourself.';
        header('Location: index.php?page=user_dashboard');
        exit;
    }

    // Step 5: Verify the sender's password.
    if (!password_verify($password, $sender['password'])) {
        $_SESSION['error_alert'] = 'Invalid password for transfer.';
        header('Location: index.php?page=user_dashboard');
        exit;
    }

    // Step 6: Check for sufficient balance.
    if ($sender['balance'] < $amount) {
        $_SESSION['error_alert'] = 'Insufficient balance.';
        header('Location: index.php?page=user_dashboard');
        exit;
    }

    // All validation passed. Now begin the database transaction.
    mysqli_begin_transaction($conn);
    try {
        // Deduct and add balance
        mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE id = $senderId");
        mysqli_query($conn, "UPDATE users SET balance = balance + $amount WHERE id = {$receiver['id']}");

        // Insert transaction record
        mysqli_query($conn, "INSERT INTO transactions (sender_id, receiver_id, amount) 
                             VALUES ($senderId, {$receiver['id']}, $amount)");

        // Add notifications
        addNotification($senderId, "You sent $$amount to {$receiverUsername}");
        addNotification($receiver['id'], "You received $$amount from {$sender['username']}");

        mysqli_commit($conn);
        $_SESSION['success_alert'] = 'Money transferred successfully.';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_alert'] = 'Transaction failed.';
    }
    
    header('Location: index.php?page=user_dashboard');
    exit;
}

function getTransactionsForUser($userId)
{
    global $conn;
    $userId = (int)$userId;
    $sql = "SELECT t.*, s.username AS sender_username, r.username AS receiver_username
            FROM transactions t
            JOIN users s ON t.sender_id = s.id
            JOIN users r ON t.receiver_id = r.id
            WHERE t.sender_id = $userId OR t.receiver_id = $userId
            ORDER BY t.transaction_date DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function bubbleSortTransactions($transactions, $key, $order = 'asc')
{
    $n = count($transactions);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            $value1 = ($key === 'transaction_date') ? strtotime($transactions[$j][$key]) : $transactions[$j][$key];
            $value2 = ($key === 'transaction_date') ? strtotime($transactions[$j + 1][$key]) : $transactions[$j + 1][$key];
            if (($order === 'asc' && $value1 > $value2) || ($order === 'desc' && $value1 < $value2)) {
                $tmp = $transactions[$j];
                $transactions[$j] = $transactions[$j + 1];
                $transactions[$j + 1] = $tmp;
            }
        }
    }
    return $transactions;
}

// ---------------- Profile Updates ----------------
function requestProfileUpdate($userId, $newUsername, $newPassword = null)
{
    global $conn;
    $userId = (int)$userId;
    $newUsername = mysqli_real_escape_string($conn, trim($newUsername));
    $hashedPassword = $newPassword ? password_hash(trim($newPassword), PASSWORD_DEFAULT) : null;

    $sql = "INSERT INTO profile_update_requests (user_id, new_username, new_password, status) 
            VALUES ($userId, '$newUsername', " . ($hashedPassword ? "'$hashedPassword'" : "NULL") . ", 'pending')";
    mysqli_query($conn, $sql);
    $_SESSION['success_alert'] = 'Profile update request submitted.';
    header('Location: index.php?page=user_dashboard');
    exit;
}

function getUserProfileUpdateRequests($userId)
{
    global $conn;

    $query = "SELECT * FROM profile_update_requests WHERE user_id = $userId";
    $result = mysqli_query($conn, $query);

    $requests = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $requests[] = $row;
    }
    return $requests;
}


function getPendingProfileUpdateRequests()
{
    global $conn;
    $sql = "SELECT pur.id, u.username, pur.new_username
            FROM profile_update_requests pur
            JOIN users u ON pur.user_id = u.id
            WHERE pur.status = 'pending'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


function acceptProfileUpdateRequest($requestId)
{
    global $conn;
    checkAuth(true);
    $requestId = (int)$requestId;

    $sql = "SELECT * FROM profile_update_requests WHERE id = $requestId";
    $result = mysqli_query($conn, $sql);
    $request = mysqli_fetch_assoc($result);

    if ($request) {
        $userId = (int)$request['user_id'];
        $newUsername = mysqli_real_escape_string($conn, $request['new_username']);
        $newPassword = $request['new_password'];

        mysqli_begin_transaction($conn);
        try {
            // Update username and password if provided
            $updateSql = "UPDATE users SET username = '$newUsername'";
            if ($newPassword) {
                $updateSql .= ", password = '$newPassword'";
            }
            $updateSql .= " WHERE id = $userId";
            mysqli_query($conn, $updateSql);

            // Mark request as accepted
            mysqli_query($conn, "UPDATE profile_update_requests SET status = 'accepted' WHERE id = $requestId");

            // Add notification
            addNotification($userId, "Your profile update request has been accepted. New username: $newUsername");

            mysqli_commit($conn);
            $_SESSION['success_alert'] = 'Profile update request accepted.';
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error_alert'] = 'Failed to accept request.';
        }
    }
    header('Location: index.php?page=admin_dashboard');
    exit;
}

function rejectProfileUpdateRequest($requestId)
{
    global $conn;
    checkAuth(true);
    $requestId = (int)$requestId;

    $sql = "SELECT * FROM profile_update_requests WHERE id = $requestId";
    $result = mysqli_query($conn, $sql);
    $request = mysqli_fetch_assoc($result);

    if ($request) {
        $userId = (int)$request['user_id'];
        $newUsername = mysqli_real_escape_string($conn, $request['new_username']);
        if (mysqli_query($conn, "UPDATE profile_update_requests SET status = 'rejected' WHERE id = $requestId")) {
            addNotification($userId, "Your profile update request for '$newUsername' has been rejected.");
            $_SESSION['success_alert'] = 'Profile update request rejected.';
        } else {
            $_SESSION['error_alert'] = 'Failed to reject request.';
        }
    }
    header('Location: index.php?page=admin_dashboard');
    exit;
}

// ---------------- Notifications ----------------
function getNotifications($userId)
{
    global $conn;
    $userId = (int)$userId;
    $sql = "SELECT * FROM notifications WHERE user_id = $userId ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function addNotification($userId, $message)
{
    global $conn;
    $userId = (int)$userId;
    $message = mysqli_real_escape_string($conn, $message);

    $sql = "INSERT INTO notifications (user_id, message, is_read) 
            VALUES ($userId, '$message', 0)";
    mysqli_query($conn, $sql);
}


function getUnreadNotificationCount($userId)
{
    global $conn;

    $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = $userId AND is_read = 0";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    return $row['count'];
}

function markNotificationsAsRead($userId)
{
    global $conn;
    $userId = (int)$userId;
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = $userId AND is_read = 0";
    mysqli_query($conn, $sql);
}