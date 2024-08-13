<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $hostel_id = $_POST['hostel_id'];

    $sql = "INSERT INTO HOSTEL_STAFF (Name, Role, Contact_Number, Email, Hostel_ID)
            VALUES ('$name', '$role', '$contact_number', '$email', '$hostel_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ManageHostelStaff.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>