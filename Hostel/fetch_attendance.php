<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$student_username = $_SESSION['username'];

// Fetch the numeric Student_ID from the STUDENT table
$student_id_query = "SELECT Student_ID FROM STUDENT WHERE Username = '$student_username'";
$student_id_result = $conn->query($student_id_query);

if ($student_id_result && $student_id_result->num_rows > 0) {
    $student_row = $student_id_result->fetch_assoc();
    $student_id = $student_row['Student_ID'];

    // Fetch the student's attendance records
    $attendance_query = "SELECT * FROM ATTENDANCE WHERE Student_ID = $student_id";
    $attendance_result = $conn->query($attendance_query);

    if ($attendance_result && $attendance_result->num_rows > 0) {
        $attendance_records = $attendance_result->fetch_all(MYSQLI_ASSOC);
    } else {
        $attendance_records = [];
    }
} else {
    echo "Error: Student not found in the database.";
    exit();
}

$conn->close();
?>