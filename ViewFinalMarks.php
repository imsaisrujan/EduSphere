<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Final Marks</title>
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
    <h1>Final Marks</h1>
    <form method="post">
      <div class="dropdown-container">
        <label for="subject-dropdown">Select Subject:</label>
        <select name="subject" id="subject-dropdown">
          <option value="">Select Subject</option>
          <?php
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'student_db');

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch subjects from the teachers_login table
            $subjectsQuery = "SELECT DISTINCT `Subject1`, `Subject2`, `Subject3` FROM teachers_login";
            $subjectsResult = $conn->query($subjectsQuery);

            if ($subjectsResult) {
                $subjects = array();
                while ($row = $subjectsResult->fetch_assoc()) {
                    for ($i = 1; $i <= 10; $i++) {
                        $subjectName = $row["Subject{$i}"];
                        if (!empty($subjectName) && !in_array($subjectName, $subjects)) {
                            echo "<option value='$subjectName'>$subjectName</option>";
                            $subjects[] = $subjectName;
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
      <button type="submit">View Marks</button>
    </form>
    <div class="table-container">
      <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate form fields
            if (empty($_POST['subject'])) {
                echo "<p>Please select a subject.</p>";
                return;
            }

            $subject = $_POST['subject'];

            try {
                // Database connection
                $conn = new mysqli('localhost', 'root', '', 'student_db');

                // Check the connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Escape values to prevent SQL injection
                $subject = $conn->real_escape_string($subject);

                // Query to select marks for all students for the selected subject
                $marksQuery = "SELECT `Hall Ticket Number`, 
                `Credits1` AS 'Credits', 
                `Grade1` AS 'Grade' 
                FROM `final_marks` 
                WHERE `Subject1` LIKE '%$subject%'
                OR `Subject2` LIKE '%$subject%'
                OR `Subject3` LIKE '%$subject%'
                OR `Subject4` LIKE '%$subject%'
                OR `Subject5` LIKE '%$subject%'
                OR `Subject6` LIKE '%$subject%'
                OR `Subject7` LIKE '%$subject%'
                OR `Subject8` LIKE '%$subject%'
                OR `Subject9` LIKE '%$subject%'
                OR `Subject10` LIKE '%$subject%'";

                $result = $conn->query($marksQuery);

                if ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<thead><tr><th>Hall Ticket Number</th><th>Credits</th><th>Grade</th></tr></thead>";
                    echo "<tbody>";

                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Hall Ticket Number'] . "</td>";
                        echo "<td>" . $row['Credits'] . "</td>";
                        echo "<td>" . $row['Grade'] . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "No records found";
                }

                $conn->close();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
      ?>
    </div>
  </div>
</body>
</html>