<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];

    $sql = "DELETE FROM HOSTEL_STAFF WHERE Staff_ID = '$staff_id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: ManageHostelStaff.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>