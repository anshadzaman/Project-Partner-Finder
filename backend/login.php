<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project_finder");

if (!isset($_POST['email']) || !isset($_POST['password'])) {
    die("Access denied");
}

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    // ✅ CORRECT PASSWORD CHECK
    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];

        if ($user['role'] == NULL) {
            header("Location: ../frontend/role_selection.html");
        } else {
            header("Location: ../frontend/dashboard.php");
        }

    } else {
        echo "Wrong password";
    }

} else {
    echo "User not found";
}
?>