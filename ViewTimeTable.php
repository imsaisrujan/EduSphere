<?php
session_start();

// Check if the user is logged in by verifying if the username is set in the session
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection settings
$servername = "localhost"; // Usually 'localhost' for XAMPP
$db_username = "root"; // Database username
$db_password = ""; // Database password
$dbname = "student_db"; // Database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$timetable = [];
$session_username = $_SESSION['username'];

// Fetch teacher name using the username from the session
$query = "SELECT `Teacher Name` FROM teachers_login WHERE username=?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
} else {
    $stmt->bind_param("s", $session_username);
    $stmt->execute();
    $stmt->bind_result($teacher_name);
    $stmt->fetch();
    $stmt->close();
}

if ($teacher_name) {
    // Fetch timetable if the teacher name is found
    $stmt = $conn->prepare("
        SELECT Section, Day, 
            Period1, Teacher1,
            Period2, Teacher2,
            Period3, Teacher3,
            Period4, Teacher4,
            Period5, Teacher5,
            Period6, Teacher6
        FROM `cse3_timetable`
    ");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
    } else {
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $timetable[$row['Day']][] = $row;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        *, html, body {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom right, #1a4e64, #2c7873, #4e8975);
        }
        .container {
            width: 90vw;
            height: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 17px 10px rgb(0 0 0 / 30%);
            border-radius: 20px;
            background: white;
            overflow: hidden;
            position: relative;
        }
        .table-container {
            width: 100%;
            height: 100%;
            overflow: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        th, td {
            padding: 5px; /* Adjust padding as necessary */
            border-bottom: 1px solid #ddd;
            max-width: 150px; /* Adjust as necessary */
            word-wrap: break-word; 
            font-size: larger;
        }
        th {
            background-color: #f0f8ff;
        }
        .period-block {
            display: block;
        }
        .lunch-break {
            background-color: #f0f8ff; /* Gold color */
            font-weight: bold;
        }
        .day-column {
            background-color: #f0f8ff; /* Alice blue color */
            font-weight: bold;
        }
        </style>
</head>
<body>
<div class="container">
    <div class="table-container">
        <table id="timetable-table">
            <thead>
                <tr>
                    <tr><th class="day-column">Day</th><th><p>Period 1</p>9:15 to 10:15</th><th><p>Period 2</p>10:15 to 11:15</th><th><p>Period 3</p>11:15 to 12:15</th><th><p>Period 4</p>13:05 to 14:05</th><th><p>Period 5</p>14:05 to 15:05</th><th><p>Period 6</p>15:05 to 16:05</th></tr>
                </tr>
            </thead>
            <tbody>
            <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach ($days as $day):
                    if (isset($timetable[$day])) {
                        foreach ($timetable[$day] as $row):
                            $matched_periods = [];
                            for ($i = 1; $i <= 6; $i++) {
                                $period = 'Period' . $i;
                                $teacher = 'Teacher' . $i;
                                if (stripos($row[$period], $teacher_name) !== false || stripos($row[$teacher], $teacher_name) !== false) {
                                    $matched_periods[$period] = $row['Section'];
                                }
                            }
                            if (!empty($matched_periods)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['Day']); ?></td>
                                    <?php for ($i = 1; $i <= 6; $i++):
                                        $period = 'Period' . $i;
                                        if (isset($matched_periods[$period])): ?>
                                            <td>
                                                <?php echo htmlspecialchars($row[$period]); ?><br>
                                                <small><?php echo htmlspecialchars($matched_periods[$period]); ?></small>
                                            </td>
                                        <?php else: ?>
                                            <td></td>
                                        <?php endif;
                                    endfor; ?>
                                </tr>
                            <?php endif;
                        endforeach;
                    } else { ?>
                        <tr>
                            <td><?php echo $day; ?></td>
                            <td colspan="6">No classes</td>
                        </tr>
                    <?php }
                endforeach; ?>
            </tbody>
        </table>
    </div>
</div></body></html>