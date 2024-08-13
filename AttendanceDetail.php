
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";

// Create connection with error handling
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$loggedInUsername = $_SESSION['username'];

// Fetch timetable subjects dynamically
$sql = "SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'student_attendance' 
        AND COLUMN_NAME NOT IN ('rollno', 'attendance_date')";
$result = $conn->query($sql);
$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[$row['COLUMN_NAME']] = $row['COLUMN_NAME'];
}


// Fetch student attendance data with aliases and COALESCE
$sql = <<<SQL
SELECT 
  COUNT(`Business Economics and Financial Analysis`) as business_economics_held,
  SUM(`Business Economics and Financial Analysis`) as business_economics,
  COUNT(`Constitution of India`) as constitution_held,
  SUM(`Constitution of India`) as constitution,
  COUNT(`Database Management Systems`) as database_systems_held,
  SUM(`Database Management Systems`) as database_systems,
  COUNT(`Database Management Systems Lab`) as database_systems_lab_held,
  SUM(`Database Management Systems Lab`) as database_systems_lab,
  COUNT(`Discrete Mathematics`) as discrete_mathematics_held,
  SUM(`Discrete Mathematics`) as discrete_mathematics,
  COUNT(`Node JS Lab`) as node_js_lab_held,
  SUM(`Node JS Lab`) as node_js_lab,
  COUNT(`Operating Systems`) as operating_systems_held,
  SUM(`Operating Systems`) as operating_systems,
  COUNT(`Operating Systems Lab`) as operating_systems_lab_held,
  SUM(`Operating Systems Lab`) as operating_systems_lab,
  COUNT(`RT Project`) as rt_project_held,
  SUM(`RT Project`) as rt_project,
  COUNT(`Software Engineering`) as software_engineering_held,
  SUM(`Software Engineering`) as software_engineering,
  COUNT(`Training & Placements`) as training_placements_held,
  SUM(`Training & Placements`) as training_placements
FROM student_attendance 
WHERE rollno = ?
SQL;
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param("s", $loggedInUsername);
$stmt->execute();
$result = $stmt->get_result();
$studentAttendance = $result->fetch_assoc();


// Map full subject names to column aliases
// Map full subject names to column aliases
$subjectKey = [
    'Business Economics and Financial Analysis' => ['held' => 'business_economics_held', 'attended' => 'business_economics'],
    'Constitution of India' => ['held' => 'constitution_held', 'attended' => 'constitution'],
    'Database Management Systems' => ['held' => 'database_systems_held', 'attended' => 'database_systems'],
    'Database Management Systems Lab' => ['held' => 'database_systems_lab_held', 'attended' => 'database_systems_lab'],
    'Discrete Mathematics' => ['held' => 'discrete_mathematics_held', 'attended' => 'discrete_mathematics'],
    'Node JS Lab' => ['held' => 'node_js_lab_held', 'attended' => 'node_js_lab'],
    'Operating Systems' => ['held' => 'operating_systems_held', 'attended' => 'operating_systems'],
    'Operating Systems Lab' => ['held' => 'operating_systems_lab_held', 'attended' => 'operating_systems_lab'],
    'RT Project' => ['held' => 'rt_project_held', 'attended' => 'rt_project'],
    'Software Engineering' => ['held' => 'software_engineering_held', 'attended' => 'software_engineering'],
    'Training & Placements' => ['held' => 'training_placements_held', 'attended' => 'training_placements']
];

// Calculate subject-wise attendance summary
$subjectSummary = [];

foreach ($subjects as $subjectName) {
    $key = $subjectKey[$subjectName] ?? null;
    if ($key === null) continue; // Skip if no mapping found

    $classesHeld = $studentAttendance[$key['held']] ?? 0;
    $classesAttended = $studentAttendance[$key['attended']] ?? 0;
    $classesAbsent = $classesHeld - $classesAttended;
    $attendancePercentage = $classesHeld !== 0 ? round(($classesAttended / $classesHeld) * 100, 2) : null;

    $subjectSummary[] = [
        'subject' => $subjectName,
        'classesHeld' => $classesHeld,
        'classesAttended' => $classesAttended,
        'classesAbsent' => $classesAbsent,
        'attendancePercentage' => $attendancePercentage !== null ? $attendancePercentage : '-'
    ];
}

// Calculate total classes held, attended, and absent
$totalHeld = array_sum(array_column($subjectSummary, 'classesHeld'));
$totalAttended = array_sum(array_column($subjectSummary, 'classesAttended'));
$totalAbsent = array_sum(array_column($subjectSummary, 'classesAbsent'));

// Calculate overall attendance percentage
$overallAttendancePercentage = $totalHeld !== 0 ? round(($totalAttended / $totalHeld) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Dashboard</title>
    <style>
        /* CSS styles remain unchanged */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        body { 
            font-family: 'Poppins', sans-serif; 
            line-height: 1.6; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(to bottom right, #1a4e64, #2c7873, #4e8975);
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
        <h1>Student Attendance Dashboard</h1>

        <h2>Subject-wise Attendance Summary</h2>
        <table>
    <tr>
        <th>S.No</th>
        <th>Subject</th>
        <th>Classes Held</th>
        <th>Classes Attended</th>
        <th>Classes Absent</th>
        <th>Attendance %</th>
    </tr>
    <?php
$sno = 1;
foreach ($subjectSummary as $subject) {
    echo "<tr>";
    echo "<td>$sno</td>";
    echo "<td>{$subject['subject']}</td>";
    echo "<td>{$subject['classesHeld']}</td>";
    echo "<td>{$subject['classesAttended']}</td>";
    echo "<td>{$subject['classesAbsent']}</td>";
    echo "<td>" . ($subject['attendancePercentage'] !== '-' ? $subject['attendancePercentage'] . '%' : '-') . "</td>";
    echo "</tr>";
    $sno++;
}
?>
<tr class="total-row">
    <td colspan="2">TOTAL</td>
    <td><?php echo $totalHeld; ?></td>
    <td><?php echo $totalAttended; ?></td>
    <td><?php echo $totalAbsent; ?></td>
    <td><?php echo $overallAttendancePercentage !== null ? $overallAttendancePercentage . '%' : '-'; ?></td>
</tr>
</table>
        <h2>Daily Attendance</h2>
        <table class="daily-table">
            <tr>
                <th>S.No</th>
                <th>Attendance Date</th>
                <th>9:15 - 10:15</th>
                <th>10:15 - 11:15</th>
                <th>11:15 - 12:15</th>
                <th>1:05 - 2:05</th>
                <th>2:05 - 3:05</th>
                <th>3:05 - 4:05</th>
            </tr>
            <?php
            $sno = 1;
            $dailyAttendance = [];
            $sql = "SELECT * FROM daily_attendance WHERE rollno = ? ORDER BY date DESC";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Failed to prepare statement: " . $conn->error);
            }
            $stmt->bind_param("s", $loggedInUsername);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $dailyAttendance[] = $row;
            }

            foreach ($dailyAttendance as $dayAttendance) {
                echo "<tr>";
                echo "<td>$sno</td>";
                echo "<td>" . $dayAttendance['date'] . "</td>";

                for ($i = 1; $i <= 6; $i++) {
                    $period = "period" . $i;
                    $status = $dayAttendance[$period] == 1 ? "P" : ($dayAttendance[$period] == 0 ? "A" : "-");
                    $class = $status == "P" ? "status-present" : ($status == "A" ? "status-absent" : "");
                    echo "<td class='$class'>$status</td>";
                }

                echo "</tr>";
                $sno++;
            }
            ?>
        </table>
    </div>
</body>
</html>