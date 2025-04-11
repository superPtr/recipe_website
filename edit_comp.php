<?php
session_start();
require('db.php');

// validate
if (!isset($_GET['comp_id']) || !is_numeric($_GET['comp_id'])) {
    $_SESSION['error'] = "Invalid competition ID";
    header("Location: view_comp5.php");
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

        $update_query = "UPDATE competitions SET
            competition_name = '$competition_name',
            description = '$description',
            start_time = '$start_time',
            end_time = '$end_time',
            status = '$status',
            allowRegister = '$allowRegister'
            WHERE competition_id = '$comp_id'";

        if(mysqli_query($con, $update_query)){
            $_SESSION['success'] = "Competition updated successfully!";
            header("Location: view_comp5.php");
            exit();
        } else{
            throw new Exception("Error updating competition.");
        }
    } catch (Exception $e) {
        $error_message = "Error: " . htmlspecialchars($e->getMessage());
    }
}

// fetch the data by comp_id
$get_query = "SELECT * FROM competitions WHERE competition_id = $comp_id";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1 class="page-heading">Edit Competition</h1>

    <?php 
        $get_query = "SELECT * FROM competitions WHERE competition_id = $comp_id";
        $result = mysqli_query($con, $get_query);
        $row = mysqli_fetch_assoc($result);
    ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="competition_name">Competition Name</label>
            <input type="text" name="competition_name" minlength="3" maxlength="250" value="<?= $row['competition_name'] ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" rows="4" cols="50" minlength="3" maxlength="65530" value="<?= $row['description'] ?>" required></textarea>
        </div>

        <div class="form-group">
            <label for="start_time">Start at:</label>
            <input type="datetime-local" name="start_time" value="<?= $row['start_time'] ?>"" required>
        </div>


        <div class="form-group">
            <label for="end_time">End at:</label>
            <input type="datetime-local" name="end_time" value="<?= $row['competition_name'] ?>" required> 
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
    </form>
</body>
</html>