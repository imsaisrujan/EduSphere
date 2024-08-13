<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $date_submitted = $_POST['date_submitted'];
    $status = $_POST['status'];
    $room_id = $_POST['room_id'];
    $student_id = $_POST['student_id'];

    $sql = "INSERT INTO MAINTENANCE_REQUEST (Description, Date_Submitted, Status, Room_ID, Student_ID)
            VALUES ('$description', '$date_submitted', '$status', '$room_id', '$student_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: MaintenanceRequest.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>