<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$student_username = $_SESSION['username'];

// Fetch the student's ID from the STUDENT table
$student_query = "SELECT Student_ID FROM STUDENT WHERE Username = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $student_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student_row = $result->fetch_assoc();
    $student_id = $student_row['Student_ID'];
} else {
    echo "Error: Student not found in the database.";
    exit();
}

// Fetch room details
$room_query = "SELECT r.* FROM ROOM r JOIN STUDENT s ON r.Room_ID = s.Room_ID WHERE s.Student_ID = ?";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$room_result = $stmt->get_result();

if ($room_result->num_rows > 0) {
    $room_details = $room_result->fetch_assoc();
} else {
    $room_details = null;
}

// Fetch attendance details
$attendance_query = "SELECT Status, COUNT(*) AS count FROM ATTENDANCE WHERE Student_ID = ? GROUP BY Status";
$stmt = $conn->prepare($attendance_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result();
$attendance_details = $attendance_result->fetch_all(MYSQLI_ASSOC);

// Fetch maintenance requests
$maintenance_query = "SELECT Status, COUNT(*) AS count FROM MAINTENANCE_REQUEST WHERE Student_ID = ? GROUP BY Status";
$stmt = $conn->prepare($maintenance_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$maintenance_result = $stmt->get_result();
$maintenance_requests = $maintenance_result->fetch_all(MYSQLI_ASSOC);

// Fetch leave applications
$leave_query = "SELECT Status, COUNT(*) AS count FROM LEAVE_REQUEST WHERE Student_ID = ? GROUP BY Status";
$stmt = $conn->prepare($leave_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$leave_result = $stmt->get_result();
$leave_applications = $leave_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #1e1e2d;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 10px;
        }
        .sidebar h2 {
            margin: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 1.5em;
        }
        .sidebar h2::before {
            content: "🏨";
            margin-right: 10px;
            font-size: 1.5em;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
        }
        .sidebar ul li {
            margin: 10px 0;
            width: 100%;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 1.1em;
            padding: 15px 20px;
            border-radius: 10px;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
            width: calc(100% - 20px);
        }
        .sidebar ul li a.active,
        .sidebar ul li a:hover {
            background-color: white;
            color: #1e1e2d;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .sidebar ul li a::before {
            content: "";
            display: inline-block;
            width: 24px;
            height: 24px;
            background-color: #6c757d;
            border-radius: 50%;
            margin-right: 10px;
            text-align: center;
            line-height: 24px;
        }
        .sidebar ul li a.active::before,
        .sidebar ul li a:hover::before {
            background-color: #1e1e2d;
            color: white;
        }
        .sidebar ul li a[href*="StudentDashboard"]::before {
            content: "📊";
        }
        .sidebar ul li a[href*="StudentRoom"]::before {
            content: "🏠";
        }
        .sidebar ul li a[href*="StudentAttendance"]::before {
            content: "👨‍🎓";
        }
        .sidebar ul li a[href*="MaintenanceRequest"]::before {
            content: "🛠️";
        }
        .sidebar ul li a[href*="StudentLeave"]::before {
            content: "📝";
        }
        .sidebar ul li a[href*="logout"]::before {
            content: "🚪";
        }
        .sidebar .logout {
            margin-top: auto;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            overflow-y: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .header .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }
        .header .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .stats, .charts {            
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .stats div, .charts div {
            flex: 1;
            min-width: 300px;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .card h3 {
            margin-top: 0;
        }
        .chart {
            height: 400px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>HOSTEL</h2>
            <ul>
                <li><a href="./StudentDashboard.php" class="active">Dashboard</a></li>
                <li><a href="./StudentRoom.php">Room Details</a></li>
                <li><a href="./StudentAttendance.php">Attendance Details</a></li>
                <li><a href="./MaintenanceRequest.php">Maintenance Requests</a></li>
                <li><a href="./StudentLeave.php">Leave Applications</a></li>
                <li class="logout"><a href="./logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                <div class="profile-icon">
                    <a href="Profile.php"><img src="./images.png" alt="Profile Picture"></a>
                </div>
            </div>
            <div class="stats">
                <div>
                    <h3>Room Details</h3>
                    <?php if ($room_details): ?>
                        <p>Room Number: <?php echo htmlspecialchars($room_details['Room_Number']); ?></p>
                        <p>Capacity: <?php echo htmlspecialchars($room_details['Capacity']); ?></p>
                    <?php else: ?>
                        <p>No room assigned yet.</p>
                    <?php endif; ?>
                </div>
            </div><br/>
            <div class="charts">
                <div class="card">
                    <h3>Attendance Status</h3>
                    <canvas id="attendanceStatusChart" class="chart"></canvas>
                </div>
                <div class="card">
                    <h3>Maintenance Requests</h3>
                    <canvas id="maintenanceRequestsChart" class="chart"></canvas>
                </div>
                <div class="card">
                <h3>Leave Applications</h3>
                    <canvas id="leaveApplicationsChart" class="chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        const attendanceStatusCtx = document.getElementById('attendanceStatusChart').getContext('2d');
        const attendanceStatusChart = new Chart(attendanceStatusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($attendance_details, 'Status')); ?>,
                datasets: [{
                    label: 'Attendance Status',
                    data: <?php echo json_encode(array_column($attendance_details, 'count')); ?>,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' days';
                            }
                        }
                    }
                }
            }
        });

        const maintenanceRequestsCtx = document.getElementById('maintenanceRequestsChart').getContext('2d');
        const maintenanceRequestsChart = new Chart(maintenanceRequestsCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($maintenance_requests, 'Status')); ?>,
                datasets: [{
                    label: 'Maintenance Requests',
                    data: <?php echo json_encode(array_column($maintenance_requests, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' requests';
                            }
                        }
                    }
                }
            }
        });

        const leaveApplicationsCtx = document.getElementById('leaveApplicationsChart').getContext('2d');
        const leaveApplicationsChart = new Chart(leaveApplicationsCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($leave_applications, 'Status')); ?>,
                datasets: [{
                    label: 'Leave Applications',
                    data: <?php echo json_encode(array_column($leave_applications, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' applications';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>