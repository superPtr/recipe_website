<?php
require('db.php');

// Update competition statuses
function updateCompetitionStatuses($con) {
    $current_time = date('Y-m-d H:i:s');
    
    $sql = "UPDATE competitions 
            SET status = CASE
                WHEN '$current_time' < start_time THEN 'upcoming'
                WHEN '$current_time' >= start_time AND '$current_time' < end_time THEN 'ongoing'
                WHEN '$current_time' >= end_time THEN 'completed'
            END";
    
    mysqli_query($con, $sql);
}

// Update statuses first
updateCompetitionStatuses($con);

// Fetch all competitions
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
    $row['formatted_start'] = date('Y-m-d H:i', strtotime($row['start_time']));
    $row['formatted_end'] = date('Y-m-d H:i', strtotime($row['end_time']));
    $competitions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Competitions</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
            min-height: 100vh;
        }

        .competitions-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        /* Tab Navigation */
        .tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .tab-button {
            padding: 12px 24px;
            border: none;
            background-color: #e0e0e0;
            color: #666;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background-color: #4CAF50;
            color: white;
        }

        /* Competition Cards Container */
        .competitions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        /* Competition Card */
        .competition-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .competition-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .competition-icon {
            font-size: 2.5em;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .competition-title {
            font-size: 1.3em;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .competition-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .competition-details {
            background-color: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .competition-details p {
            margin: 5px 0;
            color: #555;
        }

        .competition-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            text-align: center;
            width: 100%;
        }

        .competition-button:hover {
            background-color: #45a049;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
                gap: 5px;
            }

            .tab-button {
                width: 100%;
            }

            .competitions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="competitions-container">
        <h1 class="page-title">Recipe Competitions</h1>
        
        <div class="tabs">
            <button class="tab-button active" data-status="upcoming">Upcoming</button>
            <button class="tab-button" data-status="ongoing">Ongoing</button>
            <button class="tab-button" data-status="completed">Completed</button>
        </div>

        <div class="competitions-grid" id="competitions-grid"></div>
    </div>

    <script>
        const competitions = <?php echo json_encode($competitions); ?>;
        
        function displayCompetitions(status) {
            const grid = document.getElementById('competitions-grid');
            grid.innerHTML = '';
            
            const filteredCompetitions = competitions.filter(comp => comp.status === status);
            
            filteredCompetitions.forEach(competition => {
                const card = document.createElement('div');
                card.className = 'competition-card';
                
                card.innerHTML = `
                    <i class='bx bx-trophy competition-icon'></i>
                    <h2 class="competition-title">${competition.competition_name}</h2>
                    <p class="competition-description">${competition.description || 'No description available'}</p>
                    <div class="competition-details">
                        <p><i class='bx bx-calendar'></i> Start: ${competition.formatted_start}</p>
                        <p><i class='bx bx-calendar-x'></i> End: ${competition.formatted_end}</p>
                        <p><i class='bx bx-food-menu'></i> Recipes: ${competition.recipe_count}</p>
                    </div>
                    <a href="competition_page4.php?comp_id=${competition.competition_id}" 
                       class="competition-button">
                        ${getButtonText(competition.status)}
                    </a>
                `;
                
                grid.appendChild(card);
            });

            if (filteredCompetitions.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #666;">
                        No ${status} competitions available at this time.
                    </div>
                `;
            }
        }

        function getButtonText(status) {
            switch(status.toLowerCase()) {
                case 'upcoming':
                    return 'Register Now';
                case 'ongoing':
                    return 'View & Vote';
                case 'completed':
                    return 'View Results';
                default:
                    return 'View Details';
            }
        }

        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Update active tab
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                button.classList.add('active');
                
                // Display competitions for selected status
                displayCompetitions(button.dataset.status);
            });
        });

        // Initialize with upcoming competitions
        displayCompetitions('upcoming');
    </script>
</body>
</html>