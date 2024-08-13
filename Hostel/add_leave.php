<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    $status = 'Pending';

    $sql = "INSERT INTO LEAVE_REQUEST (Student_ID, Start_Date, End_Date, Reason, Status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $student_id, $start_date, $end_date, $reason, $status);

    if ($stmt->execute()) {
        echo "Leave request submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the leave application page
    header("Location: StudentLeave.php");
    exit();
}
?>