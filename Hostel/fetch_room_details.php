<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['username'];


// Fetch the student's Room_ID
$room_id_query = "SELECT Room_ID FROM STUDENT WHERE Username = '$student_id'";
$room_id_result = $conn->query($room_id_query);

if (!$room_id_result) {
    echo "Error fetching Room_ID: " . $conn->error;
    exit();
}

if ($room_id_result->num_rows > 0) {
    $room_id_row = $room_id_result->fetch_assoc();
    $room_id = $room_id_row['Room_ID'];
    
} else {
    echo "No Room_ID found for Student ID: $student_id. Please check the database records.";
    exit();
}

// Fetch the room details using the Room_ID
$room_query = "
    SELECT ROOM.Room_ID, ROOM.Room_Number, ROOM.Capacity, HOSTEL.Name AS Hostel_Name 
    FROM ROOM 
    JOIN HOSTEL ON ROOM.Hostel_ID = HOSTEL.Hostel_ID 
    WHERE ROOM.Room_ID = '$room_id'
";
$room_result = $conn->query($room_query);

if (!$room_result) {
    echo "Error executing room details query: " . $conn->error;
    exit();
}

if ($room_result->num_rows > 0) {
    $room_details = $room_result->fetch_assoc();
    
} else {
    $room_details = null;
    echo "No room details found for Room ID: $room_id. Please check the database records.";
}

$conn->close();
?>