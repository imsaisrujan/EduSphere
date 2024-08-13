<?php
include 'db.php';

$leave_id = $_POST['leave_id'];
$status = $_POST['status'];

// Update leave status in the database
$sql = "UPDATE LEAVE_REQUEST SET Status = ? WHERE Leave_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $leave_id);

if ($stmt->execute()) {
    echo "Leave status updated successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: StaffLeave.php"); // Redirect back to manage_leave_applications.php
?>