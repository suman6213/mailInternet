<?php
// This is the main entry point (front controller) for the application.
// It handles all incoming requests and routes them to the correct view.

// Include the configuration file
include_once 'config.php';

// Include the core functions
include_once 'includes/functions.php';

// Get the requested page from the URL. Defaults to 'login' if not set.
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? null;

// Handle form submissions (POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A simple switch statement to handle different form actions based on the page.
    switch ($page) {
        case 'login':
            // Handles login for regular users.
            loginUser($_POST['username'], $_POST['password']);
            break;
        case 'admin_login':
            // Handles login for admin users.
            adminLoginUser($_POST['username'], $_POST['password']);
            break;
        case 'register':
            // Handles new user registration.
            registerUser($_POST['username'], $_POST['password']);
            break;
        case 'user_dashboard':
            // Actions specific to the user dashboard.
            if ($action === 'transfer') {
                transferMoney($_SESSION['user_id'], $_POST['receiver_username'], $_POST['amount']);
            } elseif ($action === 'requestProfileUpdate') {
                // Actions for profile update requests
                checkAuth(false);
                $userId = $_SESSION['user_id'];
                $newUsername = $_POST['new_username'] ?? null;
                $newPassword = $_POST['new_password'] ?? null;
                requestProfileUpdate($userId, $newUsername, $newPassword);
            }
            break;
        case 'admin_dashboard':
            // Actions specific to the admin dashboard. This is where the create_user action is handled.
            if ($action === 'create_user') {
                createUser($_POST['username'], $_POST['password'], isset($_POST['is_admin']));
            }
            break;
    }
}

// Simple URL routing logic to include the correct view file.
// This block handles all GET requests.
switch ($page) {
    case 'login':
        include_once 'views/login.php';
        break;
    case 'admin_login':
        // New admin login view
        include_once 'views/admin_login.php';
        break;
    case 'register':
        include_once 'views/register.php';
        break;
    case 'user_dashboard':
        // Check authentication for a regular user
        checkAuth(false);
        include_once 'views/user_dashboard.php';
        break;
    case 'transaction_history':
        // Check authentication for a regular user
        checkAuth(false);
        include_once 'views/transaction_history.php';
        break;
    case 'notifications':
        // New page to show user notifications.
        checkAuth(false);
        include_once 'views/notifications.php';
        break;
    case 'admin_dashboard':
        // Check authentication for an admin user
        checkAuth(true);
        if ($action === 'delete') {
            // Handle user deletion from admin dashboard
            deleteUser($_GET['id']);
        } elseif ($action === 'acceptProfileUpdateRequest') {
            // Handle accepting a profile update request
            acceptProfileUpdateRequest($_GET['id']);
        } elseif ($action === 'rejectProfileUpdateRequest') {
            // Handle rejecting a profile update request
            rejectProfileUpdateRequest($_GET['id']);
        }
        include_once 'views/admin_dashboard.php';
        break;
    case 'logout':
        // Log the user out
        logoutUser();
        break;
    case 'search_users':
        // AJAX endpoint to search for users for the transfer form
        if (isset($_GET['query'])) {
            header('Content-Type: application/json');
            echo json_encode(searchUsers($_GET['query']));
        }
        break;
    default:
        // Default to the login page if the requested page is not found
        include_once 'views/login.php';
        break;
}
?>
