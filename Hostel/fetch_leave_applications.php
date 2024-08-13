<?php
// Database connection
include 'db.php';

// Fetch leave applications from the database
$sql = "SELECT Leave_ID, Student_ID, Start_Date, End_Date, Reason, Status FROM LEAVE_REQUEST";
$result = $conn->query($sql);

$leave_applications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $leave_applications[] = $row;
    }
}

$conn->close();
?>