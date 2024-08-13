<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $capacity = $_POST['capacity'];
    $hostel_id = $_POST['hostel_id'];

    // Check if the Hostel_ID exists in the HOSTEL table
    $result = $conn->query("SELECT * FROM hostel WHERE Hostel_ID = '$hostel_id'");
    if ($result->num_rows == 0) {
        echo "Error: Hostel_ID does not exist in the HOSTEL table.";
    } else {
        $sql = "INSERT INTO ROOM (Room_Number, Capacity, Hostel_ID)
                VALUES ('$room_number', '$capacity', '$hostel_id')";

        if ($conn->query($sql) === TRUE) {
            header("Location: ManageRooms.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>