<?php
include 'db.php';

$sql = "SELECT * FROM HOSTEL_STAFF";
$result = $conn->query($sql);

$staff = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $staff[] = $row;
    }
}
$conn->close();
?>