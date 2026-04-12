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
$role = $_SESSION['role'];

// ONLY CREATOR CAN UPDATE
if($role != 'creator'){
    die("Access Denied");
}

// GET DATA
$app_id = $_POST['app_id'] ?? null;
$action = $_POST['action'] ?? null;

if(!$app_id || !$action){
    die("Invalid Request");
}

// VALIDATE ACTION
if($action != 'accept' && $action != 'reject'){
    die("Invalid Action");
}

// CHECK IF THIS APPLICATION BELONGS TO THIS CREATOR
$check = $conn->query("
    SELECT applications.id 
    FROM applications
    JOIN projects ON applications.project_id = projects.id
    WHERE applications.id='$app_id' AND projects.creator_id='$user_id'
");

if($check->num_rows == 0){
    die("Unauthorized Action");
}

// SET STATUS
$status = ($action == 'accept') ? 'accepted' : 'rejected';

// UPDATE
$sql = "UPDATE applications SET status='$status' WHERE id='$app_id'";

if($conn->query($sql) === TRUE){

    echo "<script>
        alert('Application ".$status." successfully');
        window.location='../frontend/dashboard.php?page=applications';
    </script>";

} else {
    echo "Error: " . $conn->error;
}
?>