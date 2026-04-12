<?php
session_start();

// DB CONNECTION
$conn = new mysqli("localhost", "root", "", "project_finder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CHECK LOGIN
if(!isset($_SESSION['user_id'])){
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$project_id = $_POST['project_id'] ?? null;

if(!$project_id){
    die("Invalid Request");
}

// ❌ PREVENT CREATOR APPLYING TO OWN PROJECT
$check_owner = $conn->query("SELECT creator_id FROM projects WHERE id='$project_id'");
$row = $check_owner->fetch_assoc();

if($row['creator_id'] == $user_id){
    echo "<script>alert('You cannot apply to your own project'); window.location='../frontend/dashboard.php';</script>";
    exit();
}

// ❌ PREVENT DUPLICATE APPLICATION
$check = $conn->query("SELECT * FROM applications WHERE user_id='$user_id' AND project_id='$project_id'");

if($check->num_rows > 0){
    echo "<script>alert('You already applied to this project'); window.location='../frontend/dashboard.php';</script>";
    exit();
}

// ✅ INSERT APPLICATION
$sql = "INSERT INTO applications (user_id, project_id) VALUES ('$user_id', '$project_id')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Application sent successfully!'); window.location='../frontend/dashboard.php';</script>";
} else {
    echo "Error: " . $conn->error;
}
?>