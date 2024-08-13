<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is a staff member or admin
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Staff' && $_SESSION['role'] !== 'Admin')) {
    header("Location: login.php");
    exit();
}

// Fetch data for statistics and charts
$maintenance_requests = $conn->query("SELECT Status, COUNT(*) AS count FROM MAINTENANCE_REQUEST GROUP BY Status")->fetch_all(MYSQLI_ASSOC);
$leave_applications = $conn->query("SELECT Status, COUNT(*) AS count FROM LEAVE_REQUEST GROUP BY Status")->fetch_all(MYSQLI_ASSOC);
$total_students = $conn->query("SELECT COUNT(*) AS count FROM STUDENT")->fetch_assoc()['count'];
$total_rooms = $conn->query("SELECT COUNT(*) AS count FROM ROOM")->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
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
        .sidebar ul li a[href*="StaffDashboard"]::before {
            content: "📊";
        }
        .sidebar ul li a[href*="StaffMS"]::before {
            content: "👨‍🎓";
        }
        .sidebar ul li a[href*="StaffMRO"]::before {
            content: "🏠";
        }
        .sidebar ul li a[href*="StaffMR"]::before {
            content: "🛠️";
        }
        .sidebar ul li a[href*="StaffLeave"]::before {
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
        .stats .card, .charts .card {
            flex: 1;
            min-width: 100px;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .card h3 {
            margin-top: 0;
        }
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 300px; /* Adjust max-width */
            margin: 0 auto;
        }
        .chart {
            height: 300px; /* Adjust height */
            width: 300px;  /* Adjust width */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>HOSTEL</h2>
            <ul>
                <li><a href="./StaffDashboard.php" class="active">Dashboard</a></li>
                <li><a href="./StaffMS.php" class="<?php echo ($activePage == 'StaffMS') ? 'active' : ''; ?>">Manage Students</a></li>
                <li><a href="./StaffMRO.php" class="<?php echo ($activePage == 'StaffMRO') ? 'active' : ''; ?>">Manage Rooms</a></li>
                <li><a href="./StaffMR.php" class="<?php echo ($activePage == 'StaffMR') ? 'active' : ''; ?>">Maintenance Requests</a></li>
                <li><a href="./StaffLeave.php" class="<?php echo ($activePage == 'StaffLeave') ? 'active' : ''; ?>">Leave Applications</a></li>
                <li class="logout"><a href="./logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Staff Member'; ?></h1>
                <div class="profile-icon">
                    <a href="Profile.php"><img src="./images.png" alt="Profile Picture"></a>
                </div>
            </div>
            <div class="stats">
                <div class="card">
                    <h3>Total Students</h3>
                    <p><?php echo $total_students; ?></p>
                </div>
                <div class="card">
                    <h3>Total Rooms</h3>
                    <p><?php echo $total_rooms; ?></p>
                </div>
            </div><br/>
            <div class="charts">
                <div class="card">
                    <h3>Maintenance Requests</h3>
                    <div class="chart-container">
                        <canvas id="maintenanceRequestsChart" class="chart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h3>Leave Applications</h3>
                    <div class="chart-container">
                        <canvas id="leaveApplicationsChart" class="chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const maintenanceRequestsCtx = document.getElementById('maintenanceRequestsChart').getContext('2d');
        const maintenanceRequestsChart = new Chart(maintenanceRequestsCtx, {
            type: 'doughnut',
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
