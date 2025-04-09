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
        $query = "SELECT * FROM competitions";
        $result = mysqli_query($con, $query);

        while ($row = mysqli_fetch_assoc($result)){
            echo "<p>" . $row['competition_name'] . " " .$row['status'] . "</p>";
        }
    ?>
</body>
</html>