<?php
session_start();
session_destroy();

// REDIRECT TO LOGOUT PAGE
header("Location: ../frontend/logout.html");
exit();
?>