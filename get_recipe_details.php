<?php
require('db.php');

$recipe_id = $_GET['recipe_id'];

$query = "SELECT * FROM recipes WHERE recipe_id = $recipe_id";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $recipe = mysqli_fetch_assoc($result);
?>

<h3><?php echo htmlspecialchars($recipe['recipe_name']); ?></h3>
<form id="editRecipeForm">
    <label for="ingredient">Ingredients:</label><br>
    <textarea id="ingredient" name="ingredient" rows="4" cols="50"><?php echo htmlspecialchars($recipe['ingredient']); ?></textarea><br><br>

    <label for="steps">Steps:</label><br>
    <textarea id="steps" name="steps" rows="4" cols="50"><?php echo htmlspecialchars($recipe['steps']); ?></textarea><br><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" rows="4" cols="50"><?php echo htmlspecialchars($recipe['description']); ?></textarea><br><br>

    <input type="hidden" name="recipe_name" value="<?php echo $recipe['recipe_name']; ?>">
    <button type="button" onclick="updateRecipe()">Update Recipe</button>
</form>

<?php
} else {
    echo "<p>Recipe details not found.</p>";
}
?>