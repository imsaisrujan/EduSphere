<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Sessional Marks</title>
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
    <h1>Sessional Marks</h1>
    <form method="post">
      <div class="dropdown-container">
        <label for="subject-dropdown">Select Subject:</label>
        <select name="subject" id="subject-dropdown">
        <option value="">Select Subject</option>
        <?php
          // Database connection
          $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

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
                  for ($i = 1; $i <= 3; $i++) {
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
        <label for="session-dropdown">Select Session:</label>
        <select name="session" id="session-dropdown">
          <option value="">Select Session</option>
          <option value="Session1">Session 1</option>
          <option value="Session2">Session 2</option>
          <!-- Add more options for different sessions if needed -->
        </select>
      </div>
      <button type="submit">View Marks</button>
    </form>
    <div class="table-container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate form fields
            if (empty($_POST['subject']) || empty($_POST['section']) || empty($_POST['session'])) {
                echo "<p>Please select a subject, section, and session.</p>";
                return;
            }

            $subject = $_POST['subject'];
            $section = $_POST['section'];
            $session = $_POST['session'];

            try {
                // Database connection
                $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

                // Check the connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Escape values to prevent SQL injection
                $subject = $conn->real_escape_string($subject);
                $section = $conn->real_escape_string($section);
                $session = $conn->real_escape_string($session);

                // Determine the subject columns
                $subjectQuery = "SELECT `Hall Ticket Number`, 
                                        CASE 
                                            WHEN `Subject1 Name` LIKE '%$subject%' THEN `Subject1` 
                                            WHEN `Subject2 Name` LIKE '%$subject%' THEN `Subject2` 
                                            WHEN `Subject3 Name` LIKE '%$subject%' THEN `Subject3` 
                                            WHEN `Subject4 Name` LIKE '%$subject%' THEN `Subject4`
                                            WHEN `Subject5 Name` LIKE '%$subject%' THEN `Subject5`
                                            WHEN `Subject6 Name` LIKE '%$subject%' THEN `Subject6`
                                            WHEN `Subject7 Name` LIKE '%$subject%' THEN `Subject7`
                                            WHEN `Subject8 Name` LIKE '%$subject%' THEN `Subject8`
                                            WHEN `Subject9 Name` LIKE '%$subject%' THEN `Subject9`
                                            WHEN `Subject10 Name` LIKE '%$subject%' THEN `Subject10`
                                        END AS 'Marks',
                                        CASE 
                                            WHEN `Subject1 Name` LIKE '%$subject%' THEN `Sub1 Assignment`
                                            WHEN `Subject2 Name` LIKE '%$subject%' THEN `Sub2 Assignment`
                                            WHEN `Subject3 Name` LIKE '%$subject%' THEN `Sub3 Assignment`
                                            WHEN `Subject4 Name` LIKE '%$subject%' THEN `Sub4 Assignment`
                                            WHEN `Subject5 Name` LIKE '%$subject%' THEN `Sub5 Assignment`
                                            WHEN `Subject6 Name` LIKE '%$subject%' THEN `Sub6 Assignment`
                                            WHEN `Subject7 Name` LIKE '%$subject%' THEN `Sub7 Assignment`
                                            WHEN `Subject8 Name` LIKE '%$subject%' THEN `Sub8 Assignment`
                                            WHEN `Subject9 Name` LIKE '%$subject%' THEN `Sub9 Assignment`
                                            WHEN `Subject10 Name` LIKE '%$subject%' THEN `Sub10 Assignment`
                                        END AS 'Assignment',
                                        CASE 
                                            WHEN `Subject1 Name` LIKE '%$subject%' THEN `Sub1 Project`
                                            WHEN `Subject2 Name` LIKE '%$subject%' THEN `Sub2 Project`
                                            WHEN `Subject3 Name` LIKE '%$subject%' THEN `Sub3 Project`
                                            WHEN `Subject4 Name` LIKE '%$subject%' THEN `Sub4 Project`
                                            WHEN `Subject5 Name` LIKE '%$subject%' THEN `Sub5 Project`
                                            WHEN `Subject6 Name` LIKE '%$subject%' THEN `Sub6 Project`
                                            WHEN `Subject7 Name` LIKE '%$subject%' THEN `Sub7 Project`
                                            WHEN `Subject8 Name` LIKE '%$subject%' THEN `Sub8 Project`
                                            WHEN `Subject9 Name` LIKE '%$subject%' THEN `Sub9 Project`
                                            WHEN `Subject10 Name` LIKE '%$subject%' THEN `Sub10 Project`
                                        END AS 'Project'
                                 FROM `sessional_marks` 
                                 WHERE `Section` = '$section' AND `Session` = '$session'";
                $subjectResult = $conn->query($subjectQuery);

                if ($subjectResult->num_rows > 0) {
                  echo "<table>";
                    echo "<thead><tr><th>Hall Ticket Number</th><th>Marks</th><th>Assignment</th><th>Project</tr></thead>";
                    echo "<tbody>";
                    // Display data in the table
                    while ($row = $subjectResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Hall Ticket Number'] . "</td>";
                        echo "<td>" . $row['Marks'] . "</td>";
                        echo "<td>" . $row['Assignment'] . "</td>";
                        echo "<td>" . $row['Project'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No records found</td></tr>";
                }

                // Close the connection
                $conn->close();
            } catch (Exception $e) {
                echo "<p>Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>
    </div>
  </div>
</body>
</html>