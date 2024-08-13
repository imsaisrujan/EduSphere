<?php
session_start();
session_unset();
session_destroy();
header("Location: ./StudentLogin.html");  // Adjust the path to your login page if necessary
exit();
?>