<?php
include 'db.php';

$sql = "SELECT * FROM STUDENT";
$result = $conn->query($sql);

$students = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
$conn->close();
?>