<?php
session_start();

$conn = new mysqli("localhost", "root", "", "project_finder");

if(!isset($_SESSION['user_id'])){
    header("Location: ../frontend/login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $creator_id = $_SESSION['user_id'];

    $title = $_POST['title'];
    $domain = $_POST['domain'];
    $skills = $_POST['skills'];
    $description = $_POST['description'];
    $experience = $_POST['experience'];
    $work_hours = $_POST['work_hours'];
    $team_size = $_POST['team_size'];
    $deadline = $_POST['deadline'];

    $sql = "INSERT INTO projects 
    (creator_id, title, domain, required_skills, description, experience, work_hours, team_size, deadline)
    VALUES 
    ('$creator_id', '$title', '$domain', '$skills', '$description', '$experience', '$work_hours', '$team_size', '$deadline')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Project Created Successfully'); window.location='../frontend/dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>