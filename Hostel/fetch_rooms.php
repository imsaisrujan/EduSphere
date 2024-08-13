<?php
include 'db.php';

$sql = "SELECT * FROM ROOM";
$result = $conn->query($sql);

$rooms = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}
$conn->close();
?>