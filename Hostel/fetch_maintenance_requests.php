<?php
// Database connection
include 'db.php';

// Fetch maintenance requests from the database
$sql = "SELECT Request_ID, Description, Date_Submitted, Status, Room_ID, Student_ID FROM MAINTENANCE_REQUEST";
$result = $conn->query($sql);

$requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

$conn->close();
?>