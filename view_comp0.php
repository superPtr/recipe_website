<?php
require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition List</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body{
            background-color: #32312f;
            font-family: sans-serif;
        }
        .table-container{
            padding: 0 10%;
            margin: 40px auto 0;
        }
        .heading{
            font-size: 40px;
            text-align: center;
            color: #f1f1f1;
            margin-bottom: 40px;
        }
        .table{
            width: 100%;
            border-collapse: collapse;
        }
        .table thead{
            background-color: #ee2828;
        }
        .table thead tr th{
            font-size:14px;
            font-weight: 500;
            letter-spacing: 0.35px;
            color: #FFFFFF;
            opacity: 1;
            padding: 12px;
            vertical-align: top;
            border: 1px solid #dee23685;
        }
        .table tbody tr td {
            font-size: 14px;
            letter-spacing: 0.35px;
            font-weight: normal;
            color: #f1f1f1;
            background-color: #3c3f44;
            padding: 8px;
            text-align: center;
            border: 1px solid #dee2e685;
        }
        .table .text_open{
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.35px;
            color: #FF1046;
        }
        .table tbody tr td .btn{
            width: 130px;
            text-decoration: none;
            line-height: 35px;
            display: inline-block;
            background-color: #FF1046;
            font-weight: 500;
            color: #FFFFFF;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            font-size: 14px;
            opacity: 1;
            transition: background-color 0.3s;
        }
        .table tbody tr td .btn:hover {
            background-color: #d60d3a;
        }
        @media (max-width: 768px){
            .table thead{
                display: none;
            }
            .table, .table tbody, .table tr, .table td{
                display: block;
                width: 100%;
            }
            .table tr{
                margin-bottom: 15px;
            }
            .table tbody tr td{
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .table td:before{
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: 600;
                font-size: 14px;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="table-container">
        <h1 class="heading">Competitions</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Competition ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Allow Register</th>
                    <th>Created At</th>
                    <th>Modify</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $retrieve_query = "SELECT * FROM competitions ORDER BY created_at DESC";
                    $result = mysqli_query($con, $retrieve_query);

                    if(!$result){
                        die("Query failed: " . mysqli_error($con));
                    }

                    while($row = mysqli_fetch_assoc($result)) {
                        $comp_id = htmlspecialchars($row['competition_id']);
                        $comp_name = htmlspecialchars($row['competition_name']);
                        $description = htmlspecialchars($row['description']);
                        $start_time = htmlspecialchars($row['start_time']);
                        $end_time = htmlspecialchars($row['end_time']);
                        $status = ucfirst(htmlspecialchars($row['status']));  // Capitalize first letter
                        $created_at = htmlspecialchars($row['created_at']);
                ?>

                    <tr>
                    <td data-label="Competition ID"><?php echo $comp_id; ?></td>
                        <td data-label="Name"><?php echo $comp_name; ?></td>
                        <td data-label="Description"><?php echo $description; ?></td>
                        <td data-label="Start Time"><?php echo $start_time; ?></td>
                        <td data-label="End Time"><?php echo $end_time; ?></td>
                        <td data-label="Status"><?php echo $status; ?></td>
                        <td data-label="Allow Register"><?php echo $row['allowRegister'] ? 'Yes' : 'No'; ?></td>
                        <td data-label="Created At"><?php echo $created_at; ?></td>
                        <td data-label="Modify">
                            <a href="modify_comp.php?comp_id=<?php echo $comp_id; ?>" class="btn">Edit</a>
                        </td>
                        <td data-label="Delete">
                            <a href="delete_comp.php?comp_id=<?php echo $comp_id; ?>" class="btn" 
                               onclick="return confirm('Are you sure you want to delete this competition?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>