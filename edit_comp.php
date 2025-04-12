<?php
session_start();
require('db.php');

// Check if user is not logged in as admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

// function to show the datetime
function formatDateTime($datetime){
    try{
        $date = new DateTime($datetime);
        return $date->format('Y-m-d\TH:i');
    } catch (Exception $e) {
        return '';
    }
}

// validate
if (!isset($_GET['comp_id']) || !is_numeric($_GET['comp_id'])) {
    $_SESSION['error'] = "Invalid competition ID";
    header("Location: view_comp.php");
    exit();
}

$comp_id = $_GET['comp_id'];

// handle form submissiom
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateComp'])){
    try{
        $competition_name = mysqli_real_escape_string($con, $_POST['competition_name']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $start_time = mysqli_real_escape_string($con, $_POST['start_time']);
        $end_time = mysqli_real_escape_string($con, $_POST['end_time']);
        $status = mysqli_real_escape_string($con, $_POST['status']);
        $allowRegister = (int)$_POST['allowRegister'];

        // format the datetime values for mysql
        $formatted_start = date('Y-m-d H:i:s', strtotime($start_time));
        $formatted_end = date('Y-m-d H:i:s', strtotime($end_time));

        $update_query = "UPDATE competitions SET
            competition_name = '$competition_name',
            description = '$description',
            start_time = '$formatted_start',
            end_time = '$formatted_end',
            status = '$status',
            allowRegister = '$allowRegister'
            WHERE competition_id = '$comp_id'";

        if(mysqli_query($con, $update_query)){
            $success_message = "Competition updated successfully!";
        } else{
            throw new Exception("Error updating competition.");
        }
    } catch (Exception $e) {
        $error_message = "Error: " . htmlspecialchars($e->getMessage());
    }
}

// fetch the data by comp_id
$get_query = "SELECT * FROM competitions WHERE competition_id = $comp_id";
$get_query_result = mysqli_query($con, $get_query);

if(!$result_row = mysqli_fetch_assoc($get_query_result)){
    $_SESSION['error'] = "Competition not found!";
    header("Location: view_comp.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Competition</title>
    <style>
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }

        .page-heading {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #4CAF50;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
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
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        input[type="submit"]:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px; /* Space between buttons */
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-size: 14px;
        }
        
        .btn-cancel:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .error {
            color: red;
            background-color: #f2dede;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 4px;
            z-index: 1000;
            text-align: center;
            animation: fadeIn 0.5s ease;
            min-width: 300px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: opacity 0.5s ease;
        }

        .success-message {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
            opacity: 1;
        }

        .error-message {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
            opacity: 1;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }


    </style>
</head>
<body>
    <h1 class="page-heading">Edit Competition</h1>

    <!-- display notification / message on whether update success or not (i set to 2 sec)-->
    <?php if(isset($success_message)): ?>
        <div class="message success-message" id="successMessage">
            <?php echo $success_message; ?>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('successMessage').style.opacity = '0';
                setTimeout(function(){
                    window.location.href = 'view_comp.php';
                }, 500);
            }, 2000);
        </script>
    <?php endif; ?>

    <?php if(isset($error_message)): ?>
        <div class="message error-message" id="errorMessage">
            <?php echo $error_message; ?>
        </div>
        <script>
            // Fade out error message after 2 seconds
            setTimeout(function() {
                var errorMessage = document.getElementById('errorMessage');
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 500);
            }, 2000);
        </script>
    <?php endif; ?>

    <!-- form -->
    <form action="" method="POST">
        <div class="form-group">
            <label for="competition_name">Competition Name</label>
            <input type="text" name="competition_name" minlength="3" maxlength="250" 
                value="<?php echo htmlspecialchars($result_row['competition_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" rows="4" cols="50" minlength="3" maxlength="65530" 
                required><?php echo htmlspecialchars($result_row['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="start_time">Start at:</label>
            <input type="datetime-local" name="start_time" 
                value="<?php echo formatDateTime($result_row['start_time']); ?>" required>
        </div>


        <div class="form-group">
            <label for="end_time">End at:</label>
            <input type="datetime-local" name="end_time" 
                value="<?php echo formatDateTime($result_row['end_time']); ?>" required> 
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" required>
                <option value="upcoming" <?php echo $result_row['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                <option value="ongoing" <?php echo $result_row['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                <option value="completed" <?php echo $result_row['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="allowRegister">Allow to register</label>
            <select name="allowRegister" required>
                <option value="1" <?php echo $result_row['allowRegister'] ? 'selected' : ''; ?>>Yes</option>
                <option value="0" <?php echo !$result_row['allowRegister'] ? 'selected' : ''; ?>>No</option>
            </select>
        </div>

        <div class="button-container">
            <input type="submit" name="updateComp" value="Update">
            <a href="view_comp.php" class="btn-cancel">Cancel</a>
        </div>
    </form>

    <script>
        // Add form validation
        function validateForm() {
            const startTime = new Date(document.getElementsByName('start_time')[0].value);
            const endTime = new Date(document.getElementsByName('end_time')[0].value);
            
            if (endTime <= startTime) {
                alert('End time must be after start time');
                return false;
            }
            return true;
        }

        // Add session timeout check
        let sessionTimeout;
        function checkSessionTimeout() {
            const timeoutDuration = 1800000; // 30 minutes
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(() => {
                alert('Your session has expired. You will be redirected to the login page.');
                window.location.href = 'admin_login.php?timeout=1';
            }, timeoutDuration);
        }

        document.addEventListener('DOMContentLoaded', checkSessionTimeout);
        document.addEventListener('click', checkSessionTimeout);
        document.addEventListener('keypress', checkSessionTimeout);
    </script>
</body>
</html>