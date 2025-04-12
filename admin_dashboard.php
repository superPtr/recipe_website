<?php
session_start();

// Check if user is not logged in as admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // If last activity was more than 30 minutes ago
    session_unset();
    session_destroy();
    header("Location: admin_login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity timestamp
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        .page-heading {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #4CAF50;
        }

        .dashboard-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .menu-item {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .menu-item.create {
            border-top: 4px solid #4CAF50;
        }

        .menu-item.view {
            border-top: 4px solid #2196F3;
        }

        .menu-item.logout {
            border-top: 4px solid #f44336;
        }

        .menu-icon {
            font-size: 40px;
            margin-bottom: 10px;
            color: #555;
        }

        .menu-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .menu-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .welcome-message {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 18px;
        }

        @media (max-width: 600px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="page-heading">Admin Dashboard</h1>
        
        <div class="welcome-message">
            Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!
        </div>

        <div class="menu-grid">
            <!-- Create Competition Card -->
            <a href="create_comp.php" class="menu-item create">
                <div class="menu-icon">➕</div>
                <div class="menu-title">Create Competition</div>
                <div class="menu-description">Create a new competition and set its details</div>
            </a>

            <!-- View Competition List Card -->
            <a href="view_comp5.php" class="menu-item view">
                <div class="menu-icon">📋</div>
                <div class="menu-title">View Competition List</div>
                <div class="menu-description">View and manage existing competitions</div>
            </a>

            <!-- Logout Card -->
            <a href="javascript:void(0);" onclick="confirmLogout();" class="menu-item logout">
                <div class="menu-icon">🚪</div>
                <div class="menu-title">Logout</div>
                <div class="menu-description">Securely log out from the admin panel</div>
            </a>
        </div>
    </div>

    <script>
    function confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'admin_logout.php';
        }
    }

    // Add event listener for session timeout
    let sessionTimeout;
    function checkSessionTimeout() {
        // 30 minutes in milliseconds
        const timeoutDuration = 1800000; 
        
        // Clear existing timeout
        clearTimeout(sessionTimeout);
        
        // Set new timeout
        sessionTimeout = setTimeout(() => {
            alert('Your session has expired. You will be redirected to the login page.');
            window.location.href = 'admin_login.php?timeout=1';
        }, timeoutDuration);
    }

    // Check session timeout on page load and user activity
    document.addEventListener('DOMContentLoaded', checkSessionTimeout);
    document.addEventListener('click', checkSessionTimeout);
    document.addEventListener('keypress', checkSessionTimeout);
    </script>
</body>
</html>