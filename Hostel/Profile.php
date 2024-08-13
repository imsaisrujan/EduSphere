<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch user details based on role
if ($role == 'Student') {
    $query = "SELECT * FROM STUDENT WHERE Username = ?";
} elseif ($role == 'Staff') {
    $query = "SELECT * FROM HOSTEL_STAFF WHERE Username = ?";
} elseif ($role == 'Admin') {
    $query = "SELECT * FROM HOSTEL_STAFF WHERE Username = ?"; // Assuming the ADMIN is in USER table with role 'Admin'
} else {
    echo "Invalid role";
    exit();
}

$stmt = $conn->prepare($query);
if ($stmt === false) {
    // Debugging: Output error if prepare fails
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
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
        .sidebar ul li a[href*="admindashboard"]::before {
            content: "📊";
        }
        .sidebar ul li a[href*="staffdashboard"]::before {
            content: "📊";
        }
        .sidebar ul li a[href*="studentdashboard"]::before {
            content: "📊";
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
        .card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .card h3 {
            margin-top: 0;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            max-width: 300px;
            background-color: #e9ecef;
            color: #495057;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>HOSTEL</h2>
            <ul>
                <li><a href="./<?php echo strtolower($role); ?>dashboard.php"><?php echo ucfirst($role); ?> Dashboard</a></li>
                <li class="logout"><a href="./logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Profile</h1>
                <div class="profile-icon">
                    <img src="./images.png" alt="Profile Picture">
                </div>
            </div>
            <div class="card">
                <h3>User Details</h3>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($role); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number:</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['Contact_Number']); ?>" readonly>
                </div>
                <?php if ($role == 'Student'): ?>
                <div class="form-group">
                    <label for="course">Course:</label>
                    <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($user['Course']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($user['Year']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="room_id">Room ID:</label>
                    <input type="number" id="room_id" name="room_id" value="<?php echo htmlspecialchars($user['Room_ID']); ?>" readonly>
                </div>
                <?php elseif ($role == 'Staff' || $role == 'Admin'): ?>
                <div class="form-group">
                    <label for="position">Position:</label>
                    <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($user['Role']); ?>" readonly>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>