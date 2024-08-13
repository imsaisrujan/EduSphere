<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete related attendance records
        $sql = "DELETE FROM ATTENDANCE WHERE Student_ID = '$student_id'";
        if (!$conn->query($sql)) {
            throw new Exception("Error deleting attendance records: " . $conn->error);
        }

        // Delete the student record
        $sql = "DELETE FROM STUDENT WHERE Student_ID = '$student_id'";
        if (!$conn->query($sql)) {
            throw new Exception("Error deleting student record: " . $conn->error);
        }

        // Commit the transaction
        $conn->commit();

        header("Location: ManageStudents.php");
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        echo $e->getMessage();
    }
}

$conn->close();
?>