<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
// $is_logged_in = isset($_SESSION['user_id']);

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .main-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.5em;
            font-weight: 700;
            color: #1e3c72;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #1e3c72;
            background: #f0f4f9;
        }

        .nav-link.active {
            color: #1e3c72;
            background: #e7edf7;
        }

        .logout-btn {
            background: #1e3c72;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 20px;
        }

        .logout-btn:hover {
            background: #2a5298;
            transform: translateY(-2px);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: #1e3c72;
            font-size: 1.8em;
            cursor: pointer;
            padding: 5px;
        }

        /* Add padding to body to prevent content from hiding under fixed header */
        body {
            padding-top: 70px;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-menu {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background: white;
                flex-direction: column;
                gap: 15px;
                padding: 20px;
                transition: 0.3s ease;
            }

            .nav-menu.active {
                left: 0;
            }

            .nav-link {
                width: 100%;
                justify-content: center;
                padding: 12px;
            }

            .logout-btn {
                margin: 10px 0 0 0;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class='bx bx-restaurant'></i>
                Recipe Hub
            </a>

            <button class="mobile-menu-btn" onclick="toggleMenu()">
                <i class='bx bx-menu'></i>
            </button>

            <nav class="nav-menu" id="nav-menu">
                <a href="index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <i class='bx bx-home-alt-2'></i> Home
                </a>
                <a href="recipes.php" class="nav-link <?php echo $current_page === 'recipes.php' ? 'active' : ''; ?>">
                    <i class='bx bx-food-menu'></i> Recipes
                </a>
                <a href="meal_planning.php" class="nav-link <?php echo $current_page === 'meal_planning.php' ? 'active' : ''; ?>">
                    <i class='bx bx-calendar-edit'></i> Meal Planning
                </a>
                <a href="community.php" class="nav-link <?php echo $current_page === 'community.php' ? 'active' : ''; ?>">
                    <i class='bx bx-group'></i> Community
                </a>
                <a href="competition.php" class="nav-link <?php echo $current_page === 'competition.php' ? 'active' : ''; ?>">
                    <i class='bx bx-trophy'></i> Competition
                </a>
                <a href="logout.php" class="logout-btn">
                    <i class='bx bx-log-out'></i> Logout
                </a>
            </nav>
        </div>
    </header>

    <script>
        function toggleMenu() {
            const navMenu = document.getElementById('nav-menu');
            navMenu.classList.toggle('active');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('nav-menu');
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            if (!navMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                navMenu.classList.remove('active');
            }
        });
    </script>
</body>
</html>