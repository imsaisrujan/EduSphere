<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $room_id = $_POST['room_id'];
    $course = $_POST['course'];
    $year = $_POST['year'];

    $sql = "INSERT INTO STUDENT (Username, Password, Name, Gender, Age, Contact_Number, Email, Address, Room_ID, Course, Year)
            VALUES ('$username', '$password', '$name', '$gender', '$age', '$contact_number', '$email', '$address', '$room_id', '$course', '$year')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ManageStudents.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>