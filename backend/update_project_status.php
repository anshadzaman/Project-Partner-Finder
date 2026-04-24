<?php
session_start();

$conn = new mysqli("localhost","root","","project_finder");

$id = $_POST['project_id'];
$status = $_POST['status'];
$progress = $_POST['progress'];

$sql = "UPDATE projects 
SET project_status='$status',
progress='$progress'
WHERE id='$id'";

if($conn->query($sql)){
    $_SESSION['success'] = "Project updated successfully!";
}else{
    $_SESSION['error'] = "Update failed!";
}

header("Location: ../frontend/dashboard.php?page=projects");
exit();
?>