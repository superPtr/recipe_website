<?php
require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition Page</title>
</head>
<body>
    <?php
        $comp_id = $_GET['comp_id'];
        $query = "SELECT * FROM competitions WHERE competition_id = $comp_id";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);

        $competition_name = $row['competition_name'];
        $description = $row['description'];
        $start_date_raw = $row['start_date'];
        $start_date = date("Y-m-d", strtotime($start_date_raw));
        $end_date_raw = $row['end_date'];
        $end_date = date("Y-m-d", strtotime($end_date_raw));
    ?>

    <div>
        <h2><?php echo $competition_name ?></h2>
        <p>Start at: <?php echo $start_date ?>   End at: <?php echo $end_date ?></p>
        <p><?php echo $description ?></p>
    </div>
</body>
</html>