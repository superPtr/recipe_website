<?php
require('db.php');
require('system_header.php');

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

updateCompetitionStatuses($con);

$query = "SELECT 
    c.*, 
    (SELECT COUNT(*) FROM competition_recipes WHERE competition_id = c.competition_id) as recipe_count
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
            min-height: 100vh;
        }

        /* Header Section */
        .header-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 60px 20px;
            color: white;
            text-align: center;
            position: relative;
            margin-bottom: 40px;
        }

        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(to top right, #f5f5f5 49%, transparent 51%);
        }

        .page-title {
            font-size: 2.8em;
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .page-subtitle {
            font-size: 1.2em;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .competitions-container {
            max-width: 1200px;
            margin: -20px auto 0;
            padding: 0 20px;
            position: relative;
            z-index: 1;
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

        .tab-button:hover {
            background-color: #2a5298;
            color: white;
            transform: translateY(-2px);
        }

        .tab-button.active {
            background-color: #1e3c72;
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
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .competition-icon {
            font-size: 2.5em;
            color: #1e3c72;
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
            margin: 8px 0;
            color: #555;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .competition-details i {
            color: #1e3c72;
        }

        .competition-button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-align: center;
            width: 100%;
        }

        .competition-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
            background: white;
            border-radius: 12px;
            grid-column: 1/-1;
        }

        .empty-state i {
            font-size: 3em;
            color: #1e3c72;
            margin-bottom: 15px;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            margin-bottom: 40px;
        }

        .pagination button {
            padding: 8px 16px;
            border: 1px solid #1e3c72;
            background-color: white;
            color: #1e3c72;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .pagination button:hover {
            background-color: #1e3c72;
            color: white;
        }

        .pagination button.active {
            background-color: #1e3c72;
            color: white;
        }

        .pagination button:disabled {
            background-color: #e0e0e0;
            color: #999;
            border-color: #e0e0e0;
            cursor: not-allowed;
        }

        .pagination-info {
            color: #666;
            font-size: 14px;
            margin: 0 15px;
        }

        .competition-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
            height: 4.5em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            position: relative;
        }
        
        /* Add hover tooltip effect */
        .competition-description:hover {
            cursor: pointer;
        }

        /* Tooltip styles */
        .competition-description:hover::after {
            content: attr(title);
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
            max-width: 300px;
            z-index: 1000;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            white-space: normal;
            visibility: visible;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .competition-card {
            position: relative;
        }

        /* Message Styles */
        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 400px;
            animation: slideIn 0.5s ease;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .close-message {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .close-message:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-section {
                padding: 40px 20px;
            }

            .page-title {
                font-size: 2em;
            }

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
    <div class="header-section">
        <h1 class="page-title">Recipe Competitions</h1>
        <p class="page-subtitle">Join our cooking competitions, showcase your culinary skills, and connect with fellow food enthusiasts from around the world.</p>
    </div>

    <div class="competitions-container">
    <?php
        // Display success message
        if (isset($_GET['success'])) {
            $success_message = '';
            switch ($_GET['success']) {
                case 'submission_complete':
                    $success_message = 'Your recipe has been successfully submitted to the competition!';
                    break;
                // Add more success cases as needed
            }
            if ($success_message) {
                echo "<div class='message success-message'>
                        <i class='bx bx-check-circle'></i>
                        $success_message
                        <button class='close-message'><i class='bx bx-x'></i></button>
                    </div>";
            }
        }

        // Display error message
        if (isset($_GET['error'])) {
            $error_message = '';
            switch ($_GET['error']) {
                case 'invalid_submission':
                    $error_message = 'Invalid submission data. Please try again.';
                    break;
                case 'invalid_competition':
                    $error_message = 'This competition is not available for submissions.';
                    break;
                case 'invalid_recipe':
                    $error_message = 'Invalid recipe selection.';
                    break;
                case 'already_submitted':
                    $error_message = 'You have already submitted a recipe to this competition.';
                    break;
                case 'submission_failed':
                    $error_message = 'Failed to submit recipe. Please try again.';
                    break;
                // Add more error cases as needed
            }
            if ($error_message) {
                echo "<div class='message error-message'>
                        <i class='bx bx-error-circle'></i>
                        $error_message
                        <button class='close-message'><i class='bx bx-x'></i></button>
                    </div>";
            }
        }
        ?>

        <div class="tabs">
            <button class="tab-button active" data-status="upcoming">
                <i class='bx bx-calendar-plus'></i> Upcoming
            </button>
            <button class="tab-button" data-status="ongoing">
                <i class='bx bx-dish'></i> Ongoing
            </button>
            <button class="tab-button" data-status="completed">
                <i class='bx bx-trophy'></i> Completed
            </button>
        </div>

        <div class="competitions-grid" id="competitions-grid"></div>
        <div id="pagination" class="pagination"></div>
    </div>

    <script>
        const competitions = <?php echo json_encode($competitions); ?>;
        const ITEMS_PER_PAGE = 9;
        let currentPage = 1;
        let currentStatus = 'upcoming';

        function getButtonText(status) {
            switch(status.toLowerCase()) {
                case 'upcoming':
                    return '<i class="bx bx-user-plus"></i> Register Now';
                case 'ongoing':
                    return '<i class="bx bx-vote"></i> View & Vote';
                case 'completed':
                    return '<i class="bx bx-trophy"></i> View Results';
                default:
                    return '<i class="bx bx-info-circle"></i> View Details';
            }
        }

        function getButtonLink(competition) {
            switch(competition.status.toLowerCase()) {
                case 'upcoming':
                    return `competition_join.php?comp_id=${competition.competition_id}`;
                case 'ongoing':
                    return `view_vote.php?comp_id=${competition.competition_id}`;
                case 'completed':
                    return `comp_result.php?comp_id=${competition.competition_id}`;
                default:
                    return '#';
            }
        }
        
        function displayCompetitions(status, page) {
            const grid = document.getElementById('competitions-grid');
            grid.innerHTML = '';
            const filteredCompetitions = competitions.filter(comp => comp.status === status);
            const totalPages = Math.ceil(filteredCompetitions.length / ITEMS_PER_PAGE);
            
            if (filteredCompetitions.length === 0) {
                grid.innerHTML = `
                    <div class="empty-state">
                        <i class='bx bx-calendar-x'></i>
                        <h3>No ${status} competitions available</h3>
                        <p>Check back later for new competitions!</p>
                    </div>
                `;
                document.getElementById('pagination').style.display = 'none';
                return;
            }

            // Calculate start and end index for current page
            const startIndex = (page - 1) * ITEMS_PER_PAGE;
            const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, filteredCompetitions.length);
            const currentCompetitions = filteredCompetitions.slice(startIndex, endIndex);

            currentCompetitions.forEach(competition => {
                const card = document.createElement('div');
                card.className = 'competition-card';

                // Truncate description if it's longer than 100 characters
                const truncatedDescription = truncateText(competition.description || 'No description available', 100);
                
                card.innerHTML = `
                    <i class='bx bx-trophy competition-icon'></i>
                    <h2 class="competition-title">${competition.competition_name}</h2>
                    <p class="competition-description" title="${competition.description || 'No description available'}">
                        ${truncateText(competition.description || 'No description available', 100)}
                    </p>
                    <div class="competition-details">
                        <p><i class='bx bx-calendar'></i> Start: ${competition.formatted_start}</p>
                        <p><i class='bx bx-calendar-x'></i> End: ${competition.formatted_end}</p>
                        <p><i class='bx bx-food-menu'></i> Recipes: ${competition.recipe_count}</p>
                    </div>
                    <a href="${getButtonLink(competition)}" class="competition-button">
                        ${getButtonText(competition.status)}
                    </a>
                `;
                
                grid.appendChild(card);
            });

            // Update pagination
            updatePagination(page, totalPages, filteredCompetitions.length);
        }

        // Function to truncate text
        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substr(0, maxLength).trim() + '...';
        }

        function updatePagination(currentPage, totalPages, totalItems) {
            const pagination = document.getElementById('pagination');
            const startItem = (currentPage - 1) * ITEMS_PER_PAGE + 1;
            const endItem = Math.min(currentPage * ITEMS_PER_PAGE, totalItems);
            
            let paginationHTML = `
                <button onclick="changePage(1)" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class='bx bx-chevrons-left'></i>
                </button>
                <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class='bx bx-chevron-left'></i>
                </button>
                <span class="pagination-info">
                    Showing ${startItem}-${endItem} of ${totalItems}
                </span>
                <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class='bx bx-chevron-right'></i>
                </button>
                <button onclick="changePage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class='bx bx-chevrons-right'></i>
                </button>
            `;
            
            pagination.innerHTML = paginationHTML;
            pagination.style.display = totalPages > 1 ? 'flex' : 'none';
        }

        function changePage(page) {
            currentPage = page;
            displayCompetitions(currentStatus, currentPage);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Tab switching with pagination reset
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                button.classList.add('active');
                currentStatus = button.dataset.status;
                currentPage = 1; // Reset to first page when changing tabs
                displayCompetitions(currentStatus, currentPage);
            });
        });

        // Initialize with upcoming competitions
        displayCompetitions('upcoming', 1);

        // Message handling
        document.addEventListener('DOMContentLoaded', function() {
            const messages = document.querySelectorAll('.message');
            
            messages.forEach(message => {
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    if (message) {
                        message.style.animation = 'slideOut 0.5s ease forwards';
                        setTimeout(() => message.remove(), 500);
                    }
                }, 5000);

                // Close button handling
                const closeBtn = message.querySelector('.close-message');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        message.style.animation = 'slideOut 0.5s ease forwards';
                        setTimeout(() => message.remove(), 500);
                    });
                }
            });
        });
    </script>
</body>
</html>