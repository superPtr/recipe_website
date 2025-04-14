<?php
require('db.php');

// Simplified function to update competition statuses using mysqli style
function updateCompetitionStatuses($con) {
    $current_time = date('Y-m-d H:i:s');
    
    $sql = "UPDATE competitions 
            SET 
            status = CASE
                WHEN '$current_time' < start_time THEN 'upcoming'
                WHEN '$current_time' >= start_time AND '$current_time' < end_time THEN 'ongoing'
                WHEN '$current_time' >= end_time THEN 'completed'
            END,
            allowRegister = CASE
                WHEN '$current_time' < start_time THEN 1
                ELSE 0
            END";
    
    return mysqli_query($con, $sql);
}

// Update statuses before fetching competitions
updateCompetitionStatuses($con);

// Fetch competitions using simple mysqli query
$query = "SELECT 
    c.*, 
    (SELECT COUNT(*) FROM recipes WHERE competition_id = c.competition_id) as recipe_count
    FROM competitions c 
    ORDER BY 
    CASE 
        WHEN c.status = 'ongoing' THEN 1
        WHEN c.status = 'upcoming' THEN 2
        WHEN c.status = 'completed' THEN 3
    END,
    c.start_time ASC";

$result = mysqli_query($con, $query);
$competitions = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Format dates for display
    $row['formatted_start'] = date('Y-m-d H:i', strtotime($row['start_time']));
    $row['formatted_end'] = date('Y-m-d H:i', strtotime($row['end_time']));
    $competitions[] = $row;
    echo $row['start_time'];
}
?>

<!-- Keep your existing HTML structure -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition Page</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background: #000000;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            position: relative;
        }

        /* Tab controls wrapper */
        .tab-controls {
            display: flex;
            gap: 5px;          /* Space between tabs */
        }

        input {
            display: none;
        }

        .tab-name {
            color: #ffffff;
            font-family: poppins;
            height: 45px;
            background: rgba(255, 255, 255, 0.25);
            padding: 10px 30px;
            box-sizing: border-box;
            cursor: pointer;
            transition: 0.25s;
            border-radius: 5px 5px 0 0;  /* Round top corners */
        }

        input:checked + label .tab-name {
            background: rgb(243, 240, 58);
            color: #000000;
            font-size: 18px;
            font-weight: 800;
        }

        .tab-content {
            position: absolute;
            top: 50px;
            left: 0;
            width: 100%;
            min-height: calc(100vh - 50px); /* Subtract the height of the tabs */
            background: #ffffff;
            color: #000000;
            font-family: poppins;
            padding: 15px;
            box-sizing: border-box;
            border-radius: 5px;
            opacity: 0;
            z-index: 0;
            transition: 0.5s;
        }

        input:checked + label .tab-content {
            opacity: 1;
            display: block;  /* Show active tab */
        }

        /* Style for competition boxes( wrapper inside tab-content)*/
        .wrapper {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .box {
            background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        text-align: center;
        }

        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .box h2 {
            margin: 10px 0;
            color: #333;
        }

        .box p {
            margin: 10px 0;
            color: #666;
        }

        .competition-details {
            margin: 15px 0;
            padding: 10px;
            background: #ffffff;
            border-radius: 5px;
        }

        .competition-details p {
            margin: 5px 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }

        /* Add icon styling */
        .bx {
            font-size: 2em;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tab-controls">
            <input type="radio" name="option" id="1" checked/>
            <label for="1">
                <div class="tab-name">Upcoming</div>
            </label>

            <input type="radio" name="option" id="2"/>
            <label for="2">
                <div class="tab-name">On Going</div>
            </label>

            <input type="radio" name="option" id="3"/>
            <label for="3">
                <div class="tab-name">Completed</div>
            </label>
        </div>

        <!-- Tab content containers -->
        <div id="upcoming" class="tab-content">
            <section class="service">
                <div id="upcoming_element" class="wrapper"></div>
            </section>
        </div>

        <div id="ongoing" class="tab-content">
            <section class="service">
                <div id="ongoing_element" class="wrapper"></div>
            </section>
        </div>

        <div id="completed" class="tab-content">
            <section class="service">
                <div id="completed_element" class="wrapper"></div>
            </section>
        </div>
    </div>

    <script>
        const competitions = <?php echo json_encode($competitions); ?>;

        function organizeCompetitions() {
            document.getElementById('upcoming_element').innerHTML = '';
            document.getElementById('ongoing_element').innerHTML = '';
            document.getElementById('completed_element').innerHTML = '';

            competitions.forEach(competition => {
                let competitionElement = document.createElement('div');
                competitionElement.className = 'box';

                competitionElement.innerHTML = `
                    <i class='bx bx-baguette'></i>
                    <h2>${competition.competition_name}</h2>
                    <p>${competition.description || ''}</p>
                    <div class="competition-details">
                        <p><strong>Start:</strong> ${competition.formatted_start}</p>
                        <p><strong>End:</strong> ${competition.formatted_end}</p>
                        <p><strong>Recipes:</strong> ${competition.recipe_count}</p>
                        ${competition.status === 'upcoming' ? 
                            `<p><strong>Registration:</strong> ${competition.allowRegister == 1 ? 'Open' : 'Closed'}</p>` : ''}
                    </div>
                    <a href="competition_page4.php?comp_id=${competition.competition_id}" class="btn">
                        ${getButtonText(competition.status, competition.allowRegister)}
                    </a>
                `;

                document.getElementById(`${competition.status.toLowerCase()}_element`).appendChild(competitionElement);
            });
        }

        function getButtonText(status, allowRegister) {
            switch(status.toLowerCase()) {
                case 'upcoming':
                    return allowRegister == 1 ? 'Register Now' : 'View Details';
                case 'ongoing':
                    return 'Vote Now';
                case 'completed':
                    return 'View Results';
                default:
                    return 'Read More';
            }
        }

        // Handle tab switching
        const radioButtons = document.querySelectorAll('input[type="radio"]');
        const tabContents = document.querySelectorAll('.tab-content');

        radioButtons.forEach(radio => {
            radio.addEventListener('change', (e) => {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.style.display = 'none';
                    content.style.opacity = '0';
                });

                // Show selected tab content
                const selectedTab = document.getElementById(
                    e.target.id === '1' ? 'upcoming' :
                    e.target.id === '2' ? 'ongoing' : 'completed'
                );
                selectedTab.style.display = 'block';
                setTimeout(() => {
                    selectedTab.style.opacity = '1';
                }, 50);
            });
        });

        // Initialize the display
        document.addEventListener('DOMContentLoaded', () => {
            organizeCompetitions();
            // Show the first tab content by default
            document.getElementById('upcoming').style.display = 'block';
            document.getElementById('upcoming').style.opacity = '1';
        });
    </script>
</body>
</html>
