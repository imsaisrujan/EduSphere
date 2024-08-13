<?php include 'fetch_rooms.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
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
        .sidebar ul li a[href*="dashboard"]::before {
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
        .sidebar ul li a[href*="logout"]::before {
            content: "🚪";
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
        }
        .form-group button {
            padding: 10px 15px;
            background-color: #1e1e2d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        .form-group button:hover {
            background-color: #333;
        }
        .room-table {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .room-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .room-table th, .room-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .room-table th {
            background-color: #f0f2f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>HOSTEL</h2>
            <ul>
                <li><a href="./admindashboard.php">Dashboard</a></li>
                <li><a href="./ManageStudents.php">Manage Students</a></li>
                <li><a href="./ManageHostelStaff.php">Manage Staff</a></li>
                <li><a href="./ManageRooms.php" class="active">Manage Rooms</a></li>
                <li><a href="ManageUsers.php" class="<?php echo ($activePage == 'ManageUsers') ? 'active' : ''; ?>">Manage Users</a></li>
                <li><a href="ManageMaintenanceRequest.php" class="<?php echo ($activePage == 'MaintenanceRequests') ? 'active' : ''; ?>">Maintenance Requests</a></li>
                <li><a href="ManageLeaveApplications.php" class="<?php echo ($activePage == 'LeaveApplications') ? 'active' : ''; ?>">Leave Applications</a></li>
                <li class="logout"><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Manage Rooms</h1>
                <div class="profile-icon">
                <a href="./Profile.php"><img src="./images.png" alt="Profile Picture" ></a>
                </div>
            </div>
            <div class="card">
                <h3>Add New Room</h3>
                <form action="add_rooms.php" method="post">
                    <div class="form-group">
                        <label for="room_number">Room Number:</label>
                        <input type="text" id="room_number" name="room_number" required>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" required>
                    </div>
                    <div class="form-group">
                        <label for="hostel_id">Hostel ID:</label>
                        <input type="number" id="hostel_id" name="hostel_id" required>
                    </div>
                    <div class="form-group">
                        <button type="submit">Add Room</button>
                    </div>
                </form>
            </div>
            <div class="card">
                <h3>Room List</h3>
                <div class="room-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Room Number</th>
                                <th>Capacity</th>
                                <th>Hostel ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?php echo $room['Room_ID']; ?></td>
                                <td><?php echo $room['Room_Number']; ?></td>
                                <td><?php echo $room['Capacity']; ?></td>
                                <td><?php echo $room['Hostel_ID']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>