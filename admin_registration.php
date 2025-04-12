<?php
require('db.php');
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['register'])) {
        try {
            $admin_name = mysqli_real_escape_string($con, $_POST['admin_name']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validate password match
            if($password !== $confirm_password) {
                throw new Exception("Passwords do not match!");
            }

            // Check if username already exists (avoid duplicate admin_id)
            $check_query = "SELECT * FROM admins WHERE admin_name = '$admin_name'";
            $check_result = mysqli_query($con, $check_query);
            if(mysqli_num_rows($check_result) > 0) {
                throw new Exception("Username already exists!");
            }

            // Hash password with MD5
            $hashed_password = md5($password);

            // Insert new admin
            $query = "INSERT INTO admins (admin_name, password) VALUES ('$admin_name', '$hashed_password')";

            if(mysqli_query($con, $query)) {
                $message = "<div class='success'>Admin account created successfully!</div>";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'admin_login.php';
                        }, 2000);
                    </script>";
            } else {
                throw new Exception("Error creating account: " . mysqli_error($con));
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
    <title>Admin Registration</title>
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

        .success {
            color: green;
            background-color: #dff0d8;
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

        .password-requirements {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1 class="page-heading">Admin Registration</h1>

    <div id="messageContainer">
        <?php if(isset($message)) echo $message; ?>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label for="admin_name">Username</label>
            <input type="text" name="admin_name" required minlength="3" maxlength="255">
            <div class="password-requirements">Username must be at least 3 characters long.</div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" required minlength="6">
            <div class="password-requirements">Password must be at least 6 characters long.</div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" required>
        </div>

        <div class="button-container">
            <input type="submit" name="register" value="Register">
        </div>

        <div class="login-link">
            Already have an account? <a href="admin_login.php">Login here</a>
        </div>
    </form>

    <script>
        // Check if there's a message
        const messageContainer = document.getElementById('messageContainer');
        const form =document.getElementById('registration');

        if (messageContainer.innerHTML.trim() !== '') {
            if (messageContainer.querySelector('.error')) {
                // If error message
                setTimeout(() => {
                    messageContainer.style.display = 'none';
                    // Reset form fields but keep the username
                    const username = form.admin_name.value;
                    form.reset();
                    form.admin_name.value = username;
                }, 2000);
            }

            // success message move to upper part, handled by PHP ^^
        }
    </script>
</body>
</html>