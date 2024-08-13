<?php
session_start();
session_unset();
session_destroy();
header("Location: ./Login.html");  // Adjust the path to your login page if necessary
exit();
?>






