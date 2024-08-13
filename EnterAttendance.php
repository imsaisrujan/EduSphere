<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loggedInUsername = $_SESSION['username'];

// Fetch teacher details
$teacher = fetchTeacherDetails($conn, $loggedInUsername);
$subjectsSections = getSubjectSections($teacher);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['section'], $_POST['subject'], $_POST['attendance_date'], $_POST['attendance'], $_POST['period'])) {
    try {
        handleAttendanceUpdate($conn, $_POST);
        echo "<p style='color: green;'>Attendance updated successfully.</p>";
    } catch (Exception $e) {
        logError($e->getMessage());
        // Display detailed error message for debugging
        echo "<p style='color: red;'>An error occurred while updating attendance: " . $e->getMessage() . "</p>";
    }
}

function fetchTeacherDetails($conn, $username) {
    $sql = "SELECT * FROM teachers_login WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getSubjectSections($teacher) {
    $subjectsSections = [];
    for ($i = 1; $i <= 4; $i++) {
        if (!empty($teacher["Subject$i"]) && !empty($teacher["Section$i"])) {
            $subjectsSections[] = [
                'subject' => $teacher["Subject$i"],
                'section' => $teacher["Section$i"]
            ];
        }
    }
    return $subjectsSections;
}

function logError($errorMessage) {
    $logFile = 'error_log.txt';
    $currentDate = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$currentDate] $errorMessage\n", FILE_APPEND);
}

function handleAttendanceUpdate($conn, $postData) {
    $section = $postData['section'];
    $subject = $postData['subject'];
    $attendanceDate = $postData['attendance_date'];
    $attendance = $postData['attendance'];
    $period = $postData['period'];

    updateStudentAttendance($conn, $subject, $attendanceDate, $attendance);
    updateDailyAttendance($conn, $attendanceDate, $attendance, $period);
}

function updateStudentAttendance($conn, $subject, $attendanceDate, $attendance) {
    foreach ($attendance as $rollno => $status) {
        $status = $status == 'P' ? 1 : 0;
        $sql = "INSERT INTO student_attendance (rollno, `$subject`, attendance_date) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE `$subject` = VALUES(`$subject`)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("sis", $rollno, $status, $attendanceDate);
        if (!$stmt->execute()) {
            throw new Exception("Error executing student_attendance query: " . $stmt->error);
        }
    }
}

function updateDailyAttendance($conn, $attendanceDate, $attendance, $period) {
    foreach ($attendance as $rollno => $status) {
        $status = $status == 'P' ? 1 : 0;
        $periodColumnName = "period" . $period;
        $sql = "INSERT INTO daily_attendance (rollno, date, $periodColumnName) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE $periodColumnName = VALUES($periodColumnName)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("ssi", $rollno, $attendanceDate, $status);
        if (!$stmt->execute()) {
            throw new Exception("Error executing daily_attendance query: " . $stmt->error);
        }
        }
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enter Attendance</title>
<style>
body { 
    font-family: 'Poppins', sans-serif; 
    line-height: 1.6; 
    margin: 0; 
    padding: 20px; 
    background-color: #f0f0f0; 
    color: #333; 
}
.container { 
    max-width: 1200px; 
    margin: 0 auto; 
    background-color: white; 
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
    border-radius: 8px; 
    padding: 20px; 
}
h1, h2 { 
    text-align: center; 
    color: #1a4e64; 
    margin-bottom: 20px; 
}
table {
    width: 100%; 
    border-collapse: collapse; 
    margin-bottom: 20px; 
    background-color: white; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); 
    border-radius: 5px; 
    overflow: hidden; 
}
th, td { 
    border: 1px solid #e0e0e0; 
    padding: 12px 15px; 
    text-align: left; 
}
th { 
    background-color: #4e8975; 
    color: white; 
    font-weight: 500; 
    text-transform: uppercase; 
    font-size: 0.9em; 
    letter-spacing: 1px; 
}
tr:nth-child(even) { 
    background-color: #f8f8f8; 
}
tr:hover { 
    background-color: #f1f8f5; 
}
.absent { 
    color: #d9534f; 
}
.present { 
    color: #5cb85c; 
}
.absent-cell { 
    background-color: #ffd7d5; 
    color: #d9534f; 
    font-weight: bold; 
}
.present-cell { 
    background-color: #d4f3cb; 
    color: #5cb85c; 
}
.total-row { 
    font-weight: 600; 
    background-color: #e7e7e7 !important;
}
.daily-table td { 
    text-align: center; 
    width: 10%; 
    font-weight: 500; 
}
.status-present { 
    color: #5cb85c; 
}
.status-absent { 
    color: #d9534f; 
}
.percentage { 
    font-weight: 600; 
}
.good { 
    color: #5cb85c; 
}
.okay { 
    color: #f0ad4e; 
}
.poor { 
    color: #d9534f; 
}
</style>
</head>
<body>
<div class="container">
<h1>Enter Student Attendance</h1>
<form method="POST">
    <label for="section">Select Section:</label>
    <select name="section" id="section" onchange="this.form.submit()" required>
        <option value="">Select a section</option>
        <?php
        foreach ($subjectsSections as $subjectSection) {
            $selected = (isset($_POST['section']) && $_POST['section'] == $subjectSection['section']) ? 'selected' : '';
            echo "<option value='{$subjectSection['section']}' $selected>{$subjectSection['section']}</option>";
        }
        ?>
    </select>

    <label for="subject">Select Subject:</label>
    <select name="subject" id="subject" required>
        <?php
        if (isset($_POST['section'])) {
            $selectedSection = $_POST['section'];
            foreach ($subjectsSections as $subjectSection) {
                if ($subjectSection['section'] == $selectedSection) {
                    echo "<option value='{$subjectSection['subject']}'>{$subjectSection['subject']}</option>";
                }
            }
        } else {
            foreach ($subjectsSections as $subjectSection) {
                echo "<option value='{$subjectSection['subject']}'>{$subjectSection['subject']}</option>";
            }
        }
        ?>
    </select>

    <label for="attendance_date">Select Date:</label>
    <input type="date" name="attendance_date" id="attendance_date" required>

    <label for="period">Select Period:</label>
    <select name="period" id="period" required>
        <option value="1">Period 1</option>
        <option value="2">Period 2</option>
        <option value="3">Period 3</option>
        <option value="4">Period 4</option>
        <option value="5">Period 5</option>
        <option value="6">Period 6</option>
    </select>

    <?php
    if (isset($_POST['section'])) {
        echo "<h2>Students List</h2>";
        echo "<table>";
        echo "<tr><th>Hall Ticket No</th><th>Name</th><th>Attendance</th></tr>";

        $section = $_POST['section'];
        $sql = "SELECT * FROM students_login WHERE Section = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            logError("Failed to prepare statement: " . $conn->error);
            die("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("s", $section);
        $stmt->execute();
        $students = $stmt->get_result();

        while ($student = $students->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$student['username']}</td>";
            echo "<td>{$student['Student Name']}</td>";
            echo "<td><input type='checkbox' name='attendance[{$student['username']}]' value='P' checked> Present</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    ?>
    <button type="submit">Update Attendance</button>
</form>

<?php
// Display attendance totals if section and subject are selected
if (isset($_POST['section'], $_POST['subject'])) {
    $section = $_POST['section'];
    $subject = $_POST['subject'];

    // Fetch student attendance totals
    $sql = "SELECT s.username, s.`Student Name`, COALESCE(SUM(a.`$subject` = 1), 0) AS total_attendance
            FROM students_login s
            LEFT JOIN student_attendance a ON s.username = a.rollno
            WHERE s.Section = ?
            GROUP BY s.username, s.`Student Name`";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        logError("Failed to prepare statement: " . $conn->error);
        die("Failed to prepare statement: " . $conn->error);
    }
    $stmt->bind_param("s", $section);
    $stmt->execute();
    $attendanceResult = $stmt->get_result();

    echo "<h2>Attendance Totals</h2>";
    echo "<table>";
    echo "<tr><th>Roll No</th><th>Name</th><th>Total Attendance</th></tr>";
    while ($attendance = $attendanceResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$attendance['username']}</td>";
        echo "<td>{$attendance['Student Name']}</td>";
        echo "<td>{$attendance['total_attendance']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
</div>
</body>
</html>