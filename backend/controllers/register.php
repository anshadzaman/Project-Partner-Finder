<?php
// DB CONNECTION
$conn = new mysqli("localhost", "root", "", "project_finder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// GET FORM DATA (SAFE CHECK)
$full_name = $_POST['full_name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$qualification = $_POST['qualification'] ?? '';
$about = $_POST['about'] ?? '';

// SKILLS (MULTIPLE CHECKBOX)
$skills = isset($_POST['skills']) ? implode(", ", $_POST['skills']) : "";

// PASSWORD HASH
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// CHECK IF USER EXISTS
$check = "SELECT * FROM users WHERE email='$email' OR username='$username'";
$result = $conn->query($check);

if ($result->num_rows > 0) {
    echo "<script>
        alert('Username or Email already exists');
        window.location='../frontend/register.html';
    </script>";
    exit();
}

// INSERT DATA
$sql = "INSERT INTO users (full_name, username, email, password, qualification, skills, about)
VALUES ('$full_name', '$username', '$email', '$hashed_password', '$qualification', '$skills', '$about')";

if ($conn->query($sql) === TRUE) {
    echo "<script>
        alert('Registration Successful');
        window.location='../frontend/login.html';
    </script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>