<?php
require('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form was submitted (note the name attribute should match)
    if (isset($_POST['submit_createComp'])) {
        try {
            // Validate inputs
            if (empty($_POST['competition_name']) || empty($_POST['description'])) {
                throw new Exception("Competition name and description are required");
            }

            // Validate dates
            $start_time = strtotime($_POST['start_time']);
            $end_time = strtotime($_POST['end_time']);
            
            if ($end_time <= $start_time) {
                throw new Exception("End time must be after start time");
            }

            // Validate status
            $valid_statuses = ['upcoming', 'ongoing', 'completed'];
            if (!in_array($_POST['status'], $valid_statuses)) {
                throw new Exception("Invalid status");
            }

            // Validate allowRegister
            $allowRegister = filter_var($_POST['allowRegister'], FILTER_VALIDATE_INT);
            if ($allowRegister === false || ($allowRegister !== 0 && $allowRegister !== 1)) {
                throw new Exception("Invalid allowRegister value");
            }

            // Use prepared statement to prevent SQL injection
            $query = "INSERT INTO competitions (competition_name, description, start_time, end_time, status, allowRegister) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($con, $query);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssi", 
                    $_POST['competition_name'],
                    $_POST['description'],
                    $_POST['start_time'],
                    $_POST['end_time'],
                    $_POST['status'],
                    $allowRegister
                );

                if (mysqli_stmt_execute($stmt)) {
                    $message = "<div class='success'>Competition created successfully!</div>";
                } else {
                    throw new Exception("Error executing query: " . mysqli_stmt_error($stmt));
                }

                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparing statement: " . mysqli_error($con));
            }

        } catch (Exception $e) {
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
    <title>Create Competition</title>
    <style>
        /* Your existing CSS styles */
    </style>
</head>
<body>
    <?php if(isset($message)) echo $message; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="competition_name">Competition Name</label>
            <input type="text" name="competition_name" id="competition_name" required 
                   value="<?php echo isset($_POST['competition_name']) ? htmlspecialchars($_POST['competition_name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" cols="50" required><?php 
                echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; 
            ?></textarea>
        </div>

        <div class="form-group">
            <label for="start_time">Start at:</label>
            <input type="datetime-local" name="start_time" id="start_time" required
                   value="<?php echo isset($_POST['start_time']) ? htmlspecialchars($_POST['start_time']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="end_time">End at:</label>
            <input type="datetime-local" name="end_time" id="end_time" required
                   value="<?php echo isset($_POST['end_time']) ? htmlspecialchars($_POST['end_time']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="upcoming" <?php echo (isset($_POST['status']) && $_POST['status'] === 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                <option value="ongoing" <?php echo (isset($_POST['status']) && $_POST['status'] === 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                <option value="completed" <?php echo (isset($_POST['status']) && $_POST['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>

        <div class="form-group">
            <label for="allowRegister">Allow to register</label>
            <select name="allowRegister" id="allowRegister">
                <option value="1" <?php echo (!isset($_POST['allowRegister']) || $_POST['allowRegister'] == '1') ? 'selected' : ''; ?>>Yes</option>
                <option value="0" <?php echo (isset($_POST['allowRegister']) && $_POST['allowRegister'] == '0') ? 'selected' : ''; ?>>No</option>
            </select>
        </div>

        <div class="button-container">
            <input type="submit" name="submit_createComp" value="Create Competition">
        </div>
    </form>
</body>
</html>