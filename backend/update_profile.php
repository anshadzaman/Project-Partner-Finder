<?php
session_start();

$conn = new mysqli("localhost","root","","project_finder");

$id = $_SESSION['user_id'];

$name = $_POST['full_name'];
$skills = $_POST['skills'];
$exp = $_POST['experience'];
$domain = $_POST['preferred_domain'];
$bio = $_POST['bio'];

$conn->query("
UPDATE users SET
full_name='$name',
skills='$skills',
experience='$exp',
preferred_domain='$domain',
bio='$bio'
WHERE id='$id'
");

$_SESSION['name'] = $name;

header("Location: ../frontend/dashboard.php?page=account");
?>