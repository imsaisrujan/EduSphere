<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    die("User not logged in.");
}

// Assuming the user ID is stored in the session
$username = $_SESSION['username'];

// Fetch user role and username from the USER table
$sql = "SELECT username, role FROM USERS WHERE username = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();

if (!$user_info) {
    die("User not found.");
}

$role = $user_info['role'];

if ($role == 'Student') {
    // Fetch details from the STUDENT table
    $sql = "SELECT Username as username, Name as name, Email as email, Contact_Number as contact_number, 
            Course as course, Year as year, Room_ID as room_id 
            FROM STUDENT WHERE Username = ?";
} else if ($role == 'Staff' || $role == 'Admin') {
    // Fetch details from the HOSTEL_STAFF table for both staff and admin
    $sql = "SELECT Username as username, Name as name, Email as email, Contact_Number as contact_number, 
            Role as role_detail 
            FROM HOSTEL_STAFF WHERE Username = ?";
} else {
    die("Invalid user role.");
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>