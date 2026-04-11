<?php
session_start();

$conn = new mysqli("localhost", "root", "", "project_finder");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['role'])) {

        $role = $_POST['role'];   // ✅ FIXED
        $user_id = $_SESSION['user_id'];

        // Save role in DB
        $sql = "UPDATE users SET role='$role' WHERE id='$user_id'";
        $conn->query($sql);

        // Save in session
        $_SESSION['role'] = $role;

        echo "success";   // ✅ IMPORTANT (for fetch)

    } else {
        echo "no role received";
    }

} else {
    echo "invalid request";
}
?>