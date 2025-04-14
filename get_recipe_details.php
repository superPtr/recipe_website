<?php
require('db.php');

if (!isset($_GET['recipe_id'])) {
    echo "<div class='error-message'>No recipe selected</div>";
    exit;
}

$recipe_id = (int)$_GET['recipe_id'];

$query = "SELECT * FROM recipes WHERE recipe_id = $recipe_id";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $recipe = mysqli_fetch_assoc($result);
    ?>
    <h3><i class='bx bx-food-menu'></i> <?php echo htmlspecialchars($recipe['recipe_name']); ?></h3>
    
    <div class="recipe-preview">
        <div class="form-group">
            <label for="description">
                <i class='bx bx-message-square-detail'></i> Description:
            </label>
            <textarea id="description" name="description" readonly><?php echo htmlspecialchars($recipe['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="ingredient">
                <i class='bx bx-purchase-tag'></i> Ingredients:
            </label>
            <textarea id="ingredient" name="ingredient" readonly><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="steps">
                <i class='bx bx-list-ol'></i> Steps:
            </label>
            <textarea id="steps" name="steps" readonly><?php echo htmlspecialchars($recipe['steps']); ?></textarea>
        </div>
    </div>

    <style>
        .recipe-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1e3c72;
            font-weight: 600;
        }

        label i {
            margin-right: 8px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            background-color: white;
            color: #333;
            font-size: 0.95em;
            line-height: 1.5;
            resize: vertical;
            min-height: 80px;
        }

        textarea[readonly] {
            background-color: #f8f9fa;
            cursor: default;
        }

        h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h3 i {
            font-size: 1.2em;
        }

        .error-message {
            color: #dc3545;
            background-color: #ffe0e3;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 10px 0;
        }
    </style>
    <?php
} else {
    echo "<div class='error-message'><i class='bx bx-error-circle'></i> Recipe details not found.</div>";
}
?>