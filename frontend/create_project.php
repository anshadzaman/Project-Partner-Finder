
<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

// ONLY CREATOR CAN ACCESS
if($_SESSION['role'] != 'creator'){
    echo "Access Denied!";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Project</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/create.css?v=1">
</head>

<body>

<div class="container">

<h2>Create New Project</h2>

<form action="../backend/create_project.php" method="POST">

    <input type="text" name="title" placeholder="Project Title" required>

    <input type="text" name="domain" placeholder="Domain (AI, Web Dev, etc.)" required>

    <input type="text" name="skills" placeholder="Required Skills (comma separated)" required>

    <textarea name="description" placeholder="Project Description" required></textarea>

    <input type="text" name="experience" placeholder="Experience Required (Beginner/Intermediate/Advanced)" required>

    <input type="number" name="hours_per_day" placeholder="Work Hours per Day" required>

    <input type="date" name="deadline" required>

    <input type="number" name="team_size" placeholder="Number of Team Members Required" required>

    <button type="submit">Create Project</button>

</form>

<a href="dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>