<?php include 'fetch_staff.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Hostel Staff</title>
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
        .staff-table {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .staff-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .staff-table th, .staff-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .staff-table th {
            background-color: #f0f2f5;
        }
        .staff-table form {
            display: inline;
        }
        .delete-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .delete-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>HOSTEL</h2>
            <ul>
                <li><a href="./admindashboard.php" class="<?php echo ($activePage == 'admindashboard') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="./ManageStudents.php" class="<?php echo ($activePage == 'ManageStudents') ? 'active' : ''; ?>">Manage Students</a></li>
                <li><a href="./ManageHostelStaff.php" class="active">Manage Staff</a></li>
                <li><a href="./ManageRooms.php" class="<?php echo ($activePage == 'ManageRooms') ? 'active' : ''; ?>">Manage Rooms</a></li>
                <li><a href="ManageUsers.php" class="<?php echo ($activePage == 'ManageUsers') ? 'active' : ''; ?>">Manage Users</a></li>
                <li><a href="ManageMaintenanceRequest.php" class="<?php echo ($activePage == 'MaintenanceRequests') ? 'active' : ''; ?>">Maintenance Requests</a></li>
                <li><a href="ManageLeaveApplications.php" class="<?php echo ($activePage == 'LeaveApplications') ? 'active' : ''; ?>">Leave Applications</a></li>
                <li class="logout"><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Manage Hostel Staff</h1>
                <div class="profile-icon">
                    <a href="./Profile.php"><img src="./images.png" alt="Profile Picture"></a>
                </div>
            </div>
            <div class="card">
                <h3>Add New Staff</h3>
                <form action="add_staff.php" method="post">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <input type="text" id="role" name="role" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number:</label>
                        <input type="text" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="hostel_id">Hostel ID:</label>
                        <input type="number" id="hostel_id" name="hostel_id" required>
                    </div>
                    <div class="form-group">
                        <button type="submit">Add Staff</button>
                    </div>
                </form>
            </div>
            <div class="card">
                <h3>Hostel Staff List</h3>
                <div class="staff-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Contact Number</th>
                                <th>Email</th>
                                <th>Hostel ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $member): ?>
                            <tr>
                                <td><?php echo $member['Staff_ID']; ?></td>
                                <td><?php echo $member['Name']; ?></td>
                                <td><?php echo $member['Role']; ?></td>
                                <td><?php echo $member['Contact_Number']; ?></td>
                                <td><?php echo $member['Email']; ?></td>
                                <td><?php echo $member['Hostel_ID']; ?></td>
                                <td>
                                    <form action="delete_staff.php" method="post" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                        <input type="hidden" name="staff_id" value="<?php echo $member['Staff_ID']; ?>">
                                        <button type="submit" class="delete-button">Delete</button>
                                    </form>
                                </td>
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