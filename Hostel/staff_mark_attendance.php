<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attendance = $_POST['attendance'];
    $date = $_POST['attendance_date'];

    foreach ($attendance as $student_id) {
        $sql = "INSERT INTO ATTENDANCE (Date, Status, Student_ID) VALUES ('$date', 'Present', '$student_id')";
        $conn->query($sql);
    }

    header("Location: StaffMS.php");
}

$conn->close();
?>