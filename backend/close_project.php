<?php
session_start();

$conn = new mysqli("localhost","root","","project_finder");

if(!isset($_SESSION['user_id'])){
    header("Location: ../frontend/login.html");
    exit();
}

$id = $_GET['id'];
$user = $_SESSION['user_id'];

$conn->query("
UPDATE projects
SET status='closed'
WHERE id='$id'
AND creator_id='$user'
");

header("Location: ../frontend/dashboard.php?page=projects");
exit();
?>