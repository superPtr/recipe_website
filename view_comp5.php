<?php
session_start();
require('db.php');

if (isset($_SESSION['del_success'])) {
    echo '<div class="success-message message-fade">' . htmlspecialchars($_SESSION['del_success']) . '</div>';
    unset($_SESSION['del_success']);
}

if (isset($_SESSION['del_error'])) {
    echo '<div class="error-message message-fade">' . htmlspecialchars($_SESSION['del_error']) . '</div>';
    unset($_SESSION['del_error']);
}

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
            background-color: #fff;
            font-family: sans-serif;
        }

        .table-container {
            padding: 0 10%;
            margin: 40px auto 0;
        }

        .heading {
            font-size: 40px;
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #4CAF50;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background-color: #4CAF50;
            border: 2px solid #45a049; /* Added border for table header */
        }

        .table thead tr th {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.35px;
            color: #FFFFFF;
            opacity: 1;
            padding: 12px;
            vertical-align: top;
            border: 1px solid #ddd;
        }

        .table tbody tr td {
            font-size: 14px;
            letter-spacing: 0.35px;
            font-weight: normal;
            color: #333;
            background-color: #fff;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .table .text_open {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.35px;
            color: #4CAF50;
        }

        .table tbody tr td .btn {
            width: 130px;
            text-decoration: none;
            line-height: 35px;
            display: inline-block;
            background-color: #4CAF50;
            font-weight: 500;
            color: #FFFFFF;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            font-size: 14px;
            opacity: 1;
            transition: background-color 0.3s;
            border-radius: 4px;
        }

        .table tbody tr td .btn:hover {
            background-color: #45a049;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
        }

        .pagination a, .pagination span {
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #4CAF50;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #45a049;
        }

        .pagination .active {
            background-color: #45a049;
        }

        .pagination .disabled {
            background-color: #ddd;
            color: #666;
            cursor: not-allowed;
        }

        .truncate {
            max-width: 200px; /* Adjust this value as needed */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
        }


        .description-cell {
            position: relative;
        }

        .description-tooltip {
            visibility: hidden;
            background-color: #333;
            color: #fff;
            text-align: left;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            width: 300px; /* Adjust as needed */
            white-space: normal;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .description-cell:hover .description-tooltip {
            visibility: visible;
            opacity: 1;
        }
        .success-message {
            padding: 10px;
            margin: 10px 0;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
            border-radius: 4px;
        }

        .error-message {
            padding: 10px;
            margin: 10px 0;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
            border-radius: 4px;
        }
        .message-fade {
            animation: fadeOut 0.5s ease 2s forwards;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            min-width: 300px;
            text-align: center;
        }
        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                visibility: hidden;
            }
        }

        .success-message, .error-message {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
            }

            .table tbody tr td {
                text-align: right;
                padding-left: 50%;
                position: relative;
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
            }
            .truncate {
                max-width: none;
                white-space: normal;
            }
            
            .description-tooltip {
                display: none; /* Hide tooltip on mobile */
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
                        <td data-label="Description" class="description-cell">
                            <span class="truncate"><?php echo $description; ?></span>
                            <span class="description-tooltip"><?php echo $description; ?></span>
                        </td>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all message elements
        const messages = document.querySelectorAll('.message-fade');
        
        // Remove each message after 2.5 seconds (2 seconds delay + 0.5 seconds fade)
        messages.forEach(function(message) {
            setTimeout(function() {
                message.remove();
            }, 2500);
        });
    });
    </script>
</body>
</html>