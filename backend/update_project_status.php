<?php
session_start();

$conn = new mysqli("localhost", "root", "", "project_finder");

$project_id = $_POST['project_id'];

if(isset($_POST['close_project'])){

    $conn->query("
        UPDATE projects
        SET project_status='Closed',
            status='closed',
            progress=100
        WHERE id='$project_id'
    ");

}
else{

    $status = $_POST['status'];

    $conn->query("
        UPDATE projects
        SET project_status='$status'
        WHERE id='$project_id'
    ");
}

header("Location: ../frontend/dashboard.php?page=projects");
?>