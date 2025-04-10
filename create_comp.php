<?php
require('db.php');

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
    <title>Create Competition</title>
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