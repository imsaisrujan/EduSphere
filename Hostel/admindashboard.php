<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}


$activePage = basename($_SERVER['PHP_SELF'], ".php");

include 'db.php';


$vacant_rooms = $conn->query("SELECT COUNT(*) AS count FROM ROOM WHERE Room_ID NOT IN (SELECT DISTINCT Room_ID FROM STUDENT)")->fetch_assoc()['count'];
$total_rooms = $conn->query("SELECT COUNT(*) AS count FROM ROOM")->fetch_assoc()['count'];
$pending_complaints = $conn->query("SELECT COUNT(*) AS count FROM MAINTENANCE_REQUEST WHERE Status = 'Pending'")->fetch_assoc()['count'];
$inprogress_complaints = $conn->query("SELECT COUNT(*) AS count FROM MAINTENANCE_REQUEST WHERE Status = 'In Progress'")->fetch_assoc()['count'];
$total_tenants = $conn->query("SELECT COUNT(*) AS count FROM STUDENT")->fetch_assoc()['count'];
$students_per_year = $conn->query("SELECT Year, COUNT(*) AS count FROM STUDENT GROUP BY Year")->fetch_all(MYSQLI_ASSOC);
$attendance_stats = $conn->query("SELECT Status, COUNT(*) AS count FROM ATTENDANCE GROUP BY Status")->fetch_all(MYSQLI_ASSOC);
$gender_distribution = $conn->query("SELECT Gender, COUNT(*) AS count FROM STUDENT GROUP BY Gender")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            overflow: hidden;
            overflow-y:hidden;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            background-color: #1e1e2d;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 10px;
            position: relative;
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
            position: relative;
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
        .sidebar ul li a[href*="admindashboard"]::before {
            content: "📊";
        }
        .sidebar ul li a[href*="ManageStudents"]::before {
            content: "👨‍🎓";
        }
        .sidebar ul li a[href*="ManageHostelStaff"]::before {
            content: "👨‍🏫";
        }
        .sidebar ul li a[href*="ManageRooms"]::before {
            content: "🏠";
        }
        .sidebar ul li a[href*="ManageUsers"]::before {
            content: "👥";
        }
        .sidebar ul li a[href*="MaintenanceRequest"]::before {
            content: "🛠️";
        }
        .sidebar ul li a[href*="LeaveApplications"]::before {
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
            overflow-y: hidde;
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
            gap: 20px;
            flex-wrap: wrap;
        }
        .stats div, .charts div {
            flex: 1;
            min-width: 100px;
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
                <li><a href="admindashboard.php" class="<?php echo ($activePage == 'admindashboard') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="ManageStudents.php" class="<?php echo ($activePage == 'ManageStudents') ? 'active' : ''; ?>">Manage Students</a></li>
                <li><a href="ManageHostelStaff.php" class="<?php echo ($activePage == 'ManageHostelStaff') ? 'active' : ''; ?>">Manage Staff</a></li>
                <li><a href="ManageRooms.php" class="<?php echo ($activePage == 'ManageRooms') ? 'active' : ''; ?>">Manage Rooms</a></li>
                <li><a href="ManageUsers.php" class="<?php echo ($activePage == 'ManageUsers') ? 'active' : ''; ?>">Manage Users</a></li>
                <li><a href="ManageMaintenanceRequest.php" class="<?php echo ($activePage == 'MaintenanceRequests') ? 'active' : ''; ?>">Maintenance Requests</a></li>
                <li><a href="ManageLeaveApplications.php" class="<?php echo ($activePage == 'LeaveApplications') ? 'active' : ''; ?>">Leave Applications</a></li>
                <li class="logout"><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
                <div class="profile-icon">
                    <a href="Profile.php"><img src="./images.png" alt="Profile Picture"></a>
                </div>
            </div>
            <div class="stats">
                <div>
                    <h3>Vacant Rooms</h3>
                    <p><?php echo $vacant_rooms; ?>/<?php echo $total_rooms; ?></p>
                </div>
                <div>
                    <h3>Received Complaints</h3>
                    <p>Pending: <?php echo $pending_complaints; ?>, In Progress: <?php echo $inprogress_complaints; ?></p>
                </div>
                <div>
                    <h3>Total Tenants</h3>
                    <p><?php echo $total_tenants; ?></p>
                </div>
            </div><br/>
            <div class="charts">
                <div class="card">
                    <h3>Students per Year</h3>
                    <canvas id="studentsPerYearChart" class="chart"></canvas>
                </div>
                <div class="card">
                    <h3>Gender Distribution</h3>
                    <canvas id="genderDistributionChart" class="chart"></canvas>
                </div>
                <div class="card">
                    <h3>Attendance Status</h3>
                    <canvas id="attendanceStatusChart" class="chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        const studentsPerYearCtx = document.getElementById('studentsPerYearChart').getContext('2d');
        const studentsPerYearChart = new Chart(studentsPerYearCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($students_per_year, 'Year')); ?>,
                datasets: [{
                    label: 'Number of Students',
                    data: <?php echo json_encode(array_column($students_per_year, 'count')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const genderDistributionCtx = document.getElementById('genderDistributionChart').getContext('2d');
        const genderDistributionChart = new Chart(genderDistributionCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($gender_distribution, 'Gender')); ?>,
                datasets: [{
                    label: 'Gender Distribution',
                    data: <?php echo json_encode(array_column($gender_distribution, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
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
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' students';
                            }
                        }
                    }
                }
            }
        });

        const attendanceStatusCtx = document.getElementById('attendanceStatusChart').getContext('2d');
        const attendanceStatusChart = new Chart(attendanceStatusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($attendance_stats, 'Status')); ?>,
                datasets: [{
                    label: 'Attendance Status',
                    data: <?php echo json_encode(array_column($attendance_stats, 'count')); ?>,
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
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' records';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>