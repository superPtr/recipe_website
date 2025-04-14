<?php
require('db.php');

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

// Fetch all competitions and convert to JSON for JavaScript
$query = "SELECT * FROM competitions";
    $result = mysqli_query($con, $query);
    $competitions = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $competitions[] = $row;
    }

?>

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
            height: 100vh;
            margin: 0;
            padding: 0;
            display: grid;
            place-items: top; /*center*/
            background: #000000;
        }

        .container {
            width: 100%;
            height: 300px;
            display: flex;
            position: relative;
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
        }

        input:checked + label .tab-name {
            background: rgb(243, 240, 58);
            color: #000000;
            font-size: 18px;
            font-weight: 800;
            border-top-left-radius: 5px;
            border-top-right-radiues: 5px;
        }

        .tab-content {
            position: absolute;
            top: 50px;
            left: 0;
            width: 100%;
            min-height: 300px;
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
            z-index: 1;
        }
    </style>
</head>
<body>
    <?php
    // Fetch all competitions and convert to JSON for JavaScript
    $query = "SELECT * FROM competitions";
    $result = mysqli_query($con, $query);
    $competitions = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $competitions[] = $row;
    }
    ?>

    <div class="container">
        <input type="radio" name="option" id="1" checked/>
        <label for="1">
            <div class="tab-name">Upcoming</div>
            <div id="upcoming" class="tab-content">
                <section class="service">
                    <div id="upcoming_element" class="wrapper"></div>
                </section>
            </div>
        </label>

        <input type="radio" name="option" id="2"/>
        <label for="2">
            <div class="tab-name">On Going</div>
            <div id="ongoing" class="tab-content">
                <section class="service">
                    <div id="ongoing_element" class="wrapper"></div>
                </section>
            </div>
        </label>

        <input type="radio" name="option" id="3"/>
        <label for="3">
            <div class="tab-name">Completed</div>
            <div id="completed" class="tab-content">
                <section class="service">
                    <div id="completed_element" class="wrapper"></div>
                </section>
            </div>
        </label>
    </div>

    <script>
        // Get competitions data from PHP
        const competitions = <?php echo json_encode($competitions); ?>;
        
        // Function to organize and display competitions
        function organizeCompetitions() {
            // Clear existing content
            document.getElementById('upcoming_element').innerHTML = '';
            document.getElementById('ongoing_element').innerHTML = '';
            document.getElementById('completed_element').innerHTML = '';

            // Sort competitions by status
            competitions.forEach(competition => {
                let competitionElement = document.createElement('div');
                competitionElement.className = 'box';

                competitionElement.innerHTML = `
                    <i class='bx bx-baguette'></i>
                    <h2>${competition.competition_name}</h2>
                    <p>${competition.description || ''}</p>
                    <a href="competition_page4.php?comp_id=${competition.competition_id}" class="btn">Read More</a>
                `;

                switch(competition.status.toLowerCase()) {
                    case 'upcoming':
                        document.getElementById('upcoming_element').appendChild(competitionElement);
                        break;
                    case 'ongoing':
                        document.getElementById('ongoing_element').appendChild(competitionElement);
                        break;
                    case 'completed':
                        document.getElementById('completed_element').appendChild(competitionElement);
                        break;
                }
            });
        }

        // Handle tab switching
        const radioButtons = document.querySelectorAll('input[type="radio"]');
        const tabContents = document.querySelectorAll('.tab-content');

        radioButtons.forEach(radio => {
            radio.addEventListener('change', (e) => {
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.remove('active');
                });

                // Show selected tab content
                switch(e.target.id) {
                    case '1':
                        document.getElementById('upcoming').classList.add('active');
                        break;
                    case '2':
                        document.getElementById('ongoing').classList.add('active');
                        break;
                    case '3':
                        document.getElementById('completed').classList.add('active');
                        break;
                }
            });
        });

        // Initialize the display
        document.addEventListener('DOMContentLoaded', () => {
            organizeCompetitions();
            // Show the first tab content by default
            document.getElementById('upcoming').classList.add('active');
        });
    </script>
</body>
</html>