<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$servername = "localhost";
$db_username = "root"; // MySQL username
$db_password = ""; // MySQL password
$dbname = "hostel_db";

$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';

    if (empty($username) || empty($password)) {
        echo "Please enter both username and password.";
    } else {
        // Use prepared statements to prevent SQL injection
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Login successful
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'Admin') {
                header("Location: admindashboard.php");
                exit;
            } elseif ($row['role'] == 'Student') {
                header("Location: StudentDashboard.php");
                exit;
            } elseif ($row['role'] == 'Staff') {
                header("Location: StaffDashboard.php");
                exit;
            } else {
                echo "Unknown role";
            }
        } else {
            echo "Invalid username or password.";
        }

        $stmt->close();
    }
}

mysqli_close($conn);
?>
