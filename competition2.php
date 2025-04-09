<?php
require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition Page</title>
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
            width: 500px;
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
            <div id="upcoming" class="tab-content"></div>
        </label>

        <input type="radio" name="option" id="2" checked/>
        <label for="2">
            <div class="tab-name">On Going</div>
            <div id="ongoing" class="tab-content"></div>
        </label>

        <input type="radio" name="option" id="3" checked/>
        <label for="3">
            <div class="tab-name">Completed</div>
            <div id="completed" class="tab-content"></div>
        </label>
    </div>

    <script>
        // Get competitions data from PHP
        const competitions = <?php echo json_encode($competitions); ?>;
        console.log(competitions);
        
        // Function to organize and display competitions
        function organizeCompetitions() {
            // Clear existing content
            document.getElementById('upcoming').innerHTML = '';
            document.getElementById('ongoing').innerHTML = '';
            document.getElementById('completed').innerHTML = '';

            // Sort competitions by status
            competitions.forEach(competition => {
                const competitionElement = document.createElement('div');
                competitionElement.className = 'competition-entry';
                competitionElement.innerHTML = `
                    <p class="comp-name">${competition.competition_name}</p>
                    <!-- Add more competition details as needed -->
                `;

                switch(competition.status) {
                    case 'upcoming':
                        document.getElementById('upcoming').appendChild(competitionElement);
                        break;
                    case 'ongoing':
                        document.getElementById('ongoing').appendChild(competitionElement);
                        break;
                    case 'completed':
                        document.getElementById('completed').appendChild(competitionElement);
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