<?php
include 'db.php';

$request_id = $_POST['request_id'];
$status = $_POST['status'];

// Update request status in the database
$sql = "UPDATE MAINTENANCE_REQUEST SET Status = ? WHERE Request_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $request_id);

if ($stmt->execute()) {
    echo "Request status updated successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: ManageMaintenanceRequest.php"); // Redirect back to manage_maintenance_requests.php
?>