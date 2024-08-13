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

// Fetch the student's leave requests
$leave_requests_query = "SELECT * FROM LEAVE_REQUEST WHERE Student_ID = ?";
$stmt = $conn->prepare($leave_requests_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$leave_requests_result = $stmt->get_result();

if ($leave_requests_result->num_rows > 0) {
    $leave_requests = $leave_requests_result->fetch_all(MYSQLI_ASSOC);
} else {
    $leave_requests = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Applications</title>
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
        form {
            margin-bottom: 20px;
        }
        label, input, textarea {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }
        input[type="text"], input[type="date"], textarea {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }
        button {
            padding: 10px 15px;
            background-color: #1e1e2d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background-color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f0f2f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>HOSTEL</h2>
            <ul>
                <li><a href="./StudentDashboard.php">Dashboard</a></li>
                <li><a href="./StudentRoom.php" >Room Details</a></li>
                <li><a href="./StudentAttendance.php" >Attendance Details</a></li>
                <li><a href="./MaintenanceRequest.php" >Maintenance Requests</a></li>
                <li><a href="./StudentLeave.php" class="active">Leave Applications</a></li>
                <li class="logout"><a href="./logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
                <div class="profile-icon">
                    <a href="Profile.php"><img src="./images.png" alt="Profile Picture"></a>
                </div>
            </div>
            <div class="card">
                <h1>Apply for Leave</h1>
                <form action="add_leave.php" method="post">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>
                    <label for="reason">Reason:</label>
                    <textarea id="reason" name="reason" rows="4" required></textarea>
                    <button type="submit">Apply for Leave</button>
                </form>
            </div>
            <div class="card">
                <h2>My Leave Applications</h2>
                <table>
                    <thead>
                    <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($leave_requests) > 0): ?>
                            <?php foreach ($leave_requests as $leave_request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($leave_request['Start_Date']); ?></td>
                                <td><?php echo htmlspecialchars($leave_request['End_Date']); ?></td>
                                <td><?php echo htmlspecialchars($leave_request['Reason']); ?></td>
                                <td><?php echo htmlspecialchars($leave_request['Status']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No leave applications found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>