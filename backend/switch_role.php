<?php
session_start();

$conn = new mysqli("localhost", "root", "", "project_finder");

$user_id = $_SESSION['user_id'];

// TOGGLE ROLE
$new_role = ($_SESSION['role'] == 'creator') ? 'finder' : 'creator';

// UPDATE DB
$conn->query("UPDATE users SET role='$new_role' WHERE id='$user_id'");

// UPDATE SESSION
$_SESSION['role'] = $new_role;

header("Location: ../frontend/dashboard.php");
?>