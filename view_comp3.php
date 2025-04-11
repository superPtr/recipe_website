<?php
require('db.php');

// Pagination settings
$rows_per_page = 5;

// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Get total number of rows
$total_query = "SELECT COUNT(*) as count FROM competitions";
$total_result = mysqli_query($con, $total_query);
$total_rows = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_rows / $rows_per_page);

// query to include LIMIT and OFFSET
$retrieve_query = "SELECT * FROM competitions ORDER BY created_at DESC LIMIT $rows_per_page OFFSET $offset";
$result = mysqli_query($con, $retrieve_query);

if(!$result){
    die("Query failed: " . mysqli_error($con));
}
?>
<!-- Previous PHP code remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #32312f;
            font-family: sans-serif;
            color: #f1f1f1;
        }

        .table-container {
            padding: 0 10%;
            margin: 40px auto 0;
        }

        .heading {
            font-size: 40px;
            text-align: center;
            color: #f1f1f1;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #FF1046;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead {
            background-color: #FF1046;
        }

        .table thead tr th {
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.35px;
            color: #FFFFFF;
            opacity: 1;
            padding: 12px;
            vertical-align: top;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .table tbody tr td {
            font-size: 14px;
            letter-spacing: 0.35px;
            font-weight: normal;
            color: #f1f1f1;
            background-color: #3c3f44;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .table tbody tr:hover td {
            background-color: #4a4d51;
            transition: background-color 0.3s;
        }

        .table .text_open {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.35px;
            color: #FF1046;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: rgba(255,16,70,0.1);
        }

        .table tbody tr td .btn {
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
            border: none;
            border-radius: 4px;
            font-size: 14px;
            opacity: 1;
            transition: background-color 0.3s;
        }

        .table tbody tr td .btn:hover {
            background-color: #d60d3a;
        }

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 10px;
        }

        .pagination a, 
        .pagination span {
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #3c3f44;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-size: 14px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .pagination a:hover {
            background-color: #FF1046;
        }

        .pagination .active {
            background-color: #FF1046;
            border-color: #FF1046;
        }

        .pagination .disabled {
            background-color: #2a2d31;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table, 
            .table tbody, 
            .table tr, 
            .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 20px;
                background-color: #3c3f44;
                border-radius: 8px;
                overflow: hidden;
            }

            .table tbody tr td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }

            .table td:before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: 600;
                font-size: 14px;
                text-align: left;
                color: #FF1046;
            }

            .table tbody tr td .btn {
                width: 100%;
                margin: 5px 0;
            }

            .pagination {
                flex-wrap: wrap;
            }

            .pagination a, 
            .pagination span {
                padding: 8px 16px;
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
                while($row = mysqli_fetch_assoc($result)) {
                    $comp_id = htmlspecialchars($row['competition_id']);
                    $comp_name = htmlspecialchars($row['competition_name']);
                    $description = htmlspecialchars($row['description']);
                    $start_time = htmlspecialchars($row['start_time']);
                    $end_time = htmlspecialchars($row['end_time']);
                    $status = ucfirst(htmlspecialchars($row['status']));
                    $created_at = htmlspecialchars($row['created_at']);
                    ?>
                    
                    <tr>
                    <td data-label="Competition ID"><?php echo $comp_id; ?></td>
                        <td data-label="Name"><?php echo $comp_name; ?></td>
                        <td data-label="Description"><?php echo $description; ?></td>
                        <td data-label="Start Time"><?php echo $start_time; ?></td>
                        <td data-label="End Time"><?php echo $end_time; ?></td>
                        <td data-label="Status"><span class="text_open">[ <?php echo $status; ?> ]</span></td>
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

        <!-- Add pagination -->
        <div class="pagination">
            <?php
            // Previous button
            if ($page > 1) {
                echo "<a href='?page=".($page-1)."'>Previous</a>";
            } else {
                echo "<span class='disabled'>Previous</span>";
            }

            // Page numbers
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<span class='active'>$i</span>";
                } else {
                    echo "<a href='?page=$i'>$i</a>";
                }
            }

            // Next button
            if ($page < $total_pages) {
                echo "<a href='?page=".($page+1)."'>Next</a>";
            } else {
                echo "<span class='disabled'>Next</span>";
            }
            ?>
        </div>
    </div>
</body>
</html>