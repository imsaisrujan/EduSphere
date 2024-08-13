<?php
include 'db.php';

$query = "SELECT id, username, role FROM USERS";
$result = $conn->query($query);

if ($result === false) {
    // Output error message if query failed
    echo "Error: " . $conn->error;
    exit();
}

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    // Output message if no users found
    echo "No users found.";
}

$conn->close();
?>