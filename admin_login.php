<?php
require('db.php');
session_start();

// check if admin is already logged in
if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_name'])) {
    // If already logged in, redirect to dashboard
    header("Location: admin_dashboard.php");
    exit();
}

// check for URL parameters and set appropriate messages (timeout & logout situation)
if (isset($_GET['timeout'])) {
    $message = "<div class='warning'>Your session has expired. Please login again.</div>";
} elseif (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $message = "<div class='success'>You have been successfully logged out.</div>";
}

// check session timeout (only if an active session & i set it to 30 minutes ^^)
if (isset($_SESSION['admin_id'])) {  // Only check if admin is logged in
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        // If last activity was more than 30 minutes ago
        session_unset(); 
        session_destroy(); 
        header("Location: admin_login.php?timeout=1");
        exit();
    }
    $_SESSION['last_activity'] = time(); // Update last activity timestamp
}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['login'])) {
        try {
            $admin_name = mysqli_real_escape_string($con, $_POST['admin_name']);
            $password = md5($_POST['password']); // Convert password to MD5

            $query = "SELECT * FROM admins WHERE admin_name = '$admin_name' AND password = '$password'";
            $result = mysqli_query($con, $query);

            if(mysqli_num_rows($result) == 1) {
                $admin = mysqli_fetch_assoc($result);
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['admin_name'];
                $_SESSION['admin_login'] = true;
                $_SESSION['last_activity'] = time(); 

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $message = "<div class='error'>Invalid username or password!</div>";
            }
        } catch(Exception $e) {
            $message = "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .page-heading {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #4CAF50;
        }

        #messageContainer {
            max-width: 400px;
            margin: 20px auto;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .warning {
            color: #856404;
            background-color: #fff3cd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .error {
            color: red;
            background-color: #f2dede;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <h1 class="page-heading">Admin Login</h1>

    <!-- will display the relevant message at here -->
    <div id="messageContainer">
        <?php if(isset($message)) echo $message; ?>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label for="admin_name">Username</label>
            <input type="text" name="admin_name" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="button-container">
            <input type="submit" name="login" value="Login">
        </div>
    </form>

    <script>
        // Check if there's a message
        const messageContainer = document.getElementById('messageContainer');
        if (messageContainer.innerHTML.trim() !== '') {
            // Set different timeouts based on message type
            const messageElement = messageContainer.firstElementChild;
            let timeout = 3000; // default timeout

            if (messageElement.classList.contains('success')) {
                timeout = 2000; // shorter timeout for success messages
            } else if (messageElement.classList.contains('warning')) {
                timeout = 4000; // longer timeout for warning messages
            }

            // Set timeout to add fade-out class after 2.5 seconds
            setTimeout(() => {
                messageContainer.classList.add('fade-out');
            }, 2500);

            // Remove the message after fade animation (3 seconds total)
            setTimeout(() => {
                messageContainer.innerHTML = '';
                messageContainer.classList.remove('fade-out');
            }, 3000);
        }
    </script>
</body>
</html>