<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student List</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    *,
    html,
    body {
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
      height: 80vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 40px 20px 20px;
      box-shadow: 0 0 17px 10px rgb(0 0 0 / 30%);
      border-radius: 20px;
      background: white;
      overflow: hidden;
    }
    h1 {
      font-weight: 600;
      text-align: center;
      margin-bottom: 20px;
    }
    .dropdown-container {
      width: 100%;
      display: flex;
      justify-content: center;
      margin-bottom: 10px;
    }
    select, .info {
      padding: 10px;
      margin: 0 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      background-color: #4e8975;
      color: white;
      margin-top: 20px;
      align-self: center;
    }
    .table-container {
      width: 100%;
      height: 300px; /* Adjust the height as needed */
      overflow-y: auto;
      overflow-x: auto;
      margin-top: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 10px;
      text-align: left;
      white-space: nowrap; /* Prevent text from wrapping */
    }
    th{
      background-color: #4e8975;
      color: white;
    }
    form {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    label {
  margin-right: 10px;
  display: flex; /* Added to align label text vertically */
  align-items: center; /* Added to align label text vertically */
}

    .dropdown-container select {
      flex: 1;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Student List</h1>
    <form method="post">
      <div class="dropdown-container">
        <label for="section-dropdown">Select Section:</label>
        <select name="section" id="section-dropdown">
        <option value="">Select Section</option>
        <?php
          // Database connection
          $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

          // Check the connection
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }

          // Fetch sections from the teachers_login table
          $sectionsQuery = "SELECT DISTINCT `Section1`, `Section2`, `Section3` FROM teachers_login";
          $sectionsResult = $conn->query($sectionsQuery);

          if ($sectionsResult) {
              $sections = array();
              while ($row = $sectionsResult->fetch_assoc()) {
                  for ($i = 1; $i <= 3; $i++) {
                      $section = $row["Section{$i}"];
                      if (!empty($section) && !in_array($section, $sections)) {
                          echo "<option value='$section'>$section</option>";
                          $sections[] = $section;
                      }
                  }
              }
          } else {
              echo "<option value=''>Error: " . $conn->error . "</option>";
          }

          // Close the connection
          $conn->close();
          ?>
        </select>
      </div>
      <button type="submit">View Students</button>
    </form>
    <div class="table-container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate form fields
            if (empty($_POST['section'])) {
                echo "<tr><td colspan='4'>Please select a section.</td></tr>";
            } else {
                $section = $_POST['section'];

                try {
                    // Database connection
                    $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

                    // Check the connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Escape values to prevent SQL injection
                    $section = $conn->real_escape_string($section);

                    // Fetch data from students_login table based on the selected section
                    $query = "SELECT `username` as 'Hall Ticket Number', `Student Name`, `Phone Number`, `Email Id` FROM `students_login` WHERE `Section` = '$section'";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                      echo "<table>";
                      echo "<thead><tr><th>Hall Ticket Number</th><th>Student Name</th><th>Phone Number</th><th>Email Id</tr></thead>";
                      echo "<tbody>";
                        // Display data in the table
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['Hall Ticket Number'] . "</td>";
                            echo "<td>" . $row['Student Name'] . "</td>";
                            echo "<td>" . $row['Phone Number'] . "</td>";
                            echo "<td>" . $row['Email Id'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No students found</td></tr>";
                    }

                    // Close the connection
                    $conn->close();
                } catch (Exception $e) {
                    echo "<tr><td colspan='4'>Error: " . $e->getMessage() . "</td></tr>";
                }
            }
        }
        ?>
    </div>
  </body>
</html>

