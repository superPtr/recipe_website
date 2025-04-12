<?php
require('db.php');

session_start(); // Start the session

// Check if user is not logged in as admin
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: admin_login.php"); // Redirect to login page
    exit();
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time stamp

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['createComp'])){
        try{
            $competition_name = $_POST['competition_name'];
            $description = $_POST['description'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            $status = $_POST['status'];
            $allowRegister = $_POST['allowRegister'];
            
            $query = "INSERT INTO competitions(competition_name, description, start_time, end_time, status, allowRegister)
                        VALUES ('$competition_name', '$description', '$start_time', '$end_time', '$status', '$allowRegister')";

            //execute and handle the result
            if(mysqli_query($con, $query)){
                $message = "<div class='success'>Competition created successfully!</div>";
            } else{
                throw new Exception("Error executing query" . mysqli_error($con));
            }
        } catch(Exception $e){
            $message = "<div class='error'>Error: " . htmlspecialchars($e.getMessage()) . "</div>";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create Competition</title>
    <style>
        form {
            max-width: 600px;
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
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500; /* Add this for better readability */
        }

        input[type="text"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Add this to prevent overflow */
        }

        input[type="text"]:focus,
        input[type="datetime-local"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76,175,80,0.2);
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: auto;
            font-size: 16px; /* Add this for better readability */
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-cancel {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 16px; /* Match submit button */
        }

        .btn-cancel:hover {
            background-color: #d32f2f;
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
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
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
    <h1 class="page-heading">Create Competition</h1>

    <!--show the relevant messages depends on success or not-->
    <div id="messageContainer">
        <?php if(isset($message)) echo $message;?>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label for="competition_name">Competition Name</label>
            <input type="text" name="competition_name" minlength="3" maxlength="250" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" rows="4" cols="50" minlength="3" maxlength="65530" required></textarea>
        </div>

        <div class="form-group">
            <label for="start_time">Start at:</label>
            <input type="datetime-local" name="start_time" required>
        </div>


        <div class="form-group">
            <label for="end_time">End at:</label>
            <input type="datetime-local" name="end_time" required> 
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" required>
                <option value="upcoming" selected="selected">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="allowRegister">Allow to register</label>
            <select name="allowRegister" required>
                <option value="1" selected="selected">Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="button-container">
            <input type="submit" name="createComp" value="Create Competition">
            <a href="admin_dashboard.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
    
    <!--for message timeout (3sec)-->
    <script>
        // Check if there's a message
        const messageContainer = document.getElementById('messageContainer');
        if (messageContainer.innerHTML.trim() !== '') {
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