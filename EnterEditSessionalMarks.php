<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter/Edit Sessional Marks</title>
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
      height: 300px; 
      overflow-y: auto;
      overflow-x: auto;
      margin-top: 20px;
    }
    form {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    label {
      margin-right: 10px;
      display: flex;
      align-items: center; 
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
            $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

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
            $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

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
      </div><br/>
      <div class="dropdown-container">
        <label for="hall-ticket">Hall Ticket Number:</label>
        <input type="text" id="hall-ticket" name="hall_ticket" placeholder="Enter Hall Ticket Number">
      </div><br/>
      <div class="dropdown-container">
        <label for="marks">Marks:</label>
        <input type="text" id="marks" name="marks" placeholder="Enter Marks">
      </div><br/>
      <div class="dropdown-container">
        <label for="assignment">Assignment:</label>
        <input type="text" id="assignment" name="assignment" placeholder="Enter Assignment Marks">
      </div><br/>
      <div class="dropdown-container">
        <label for="project">Project:</label>
        <input type="text" id="project" name="project" placeholder="Enter Project Marks">
      </div>
      <button type="submit">Update Marks</button>
    </form>
    <?php
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $subject = $_POST['subject'];
        $section = $_POST['section'];
        $session = $_POST['session'];
        $hall_ticket = $_POST['hall_ticket'];
        $marks = $_POST['marks'];
        $assignment = $_POST['assignment'];
        $project = $_POST['project'];

        $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        // Check if hall ticket number already exists
        $checkQuery = "SELECT * FROM sessional_marks WHERE `Hall Ticket Number` = ?";
        $stmt = $conn->prepare($checkQuery);
        if ($stmt === false) {
          die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("s", $hall_ticket);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          // Hall ticket number exists, update the record
          $updateQuery = "UPDATE sessional_marks SET `Subject1 Name` = ?, `Section` = ?, `Session` = ?, `Subject1` = ?, `Sub1 Assignment` = ?, `Sub1 Project` = ? WHERE `Hall Ticket Number` = ?";
          $stmt = $conn->prepare($updateQuery);
          if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
          }
          $stmt->bind_param("sssssss", $subject, $section, $session, $marks, $assignment, $project, $hall_ticket);
        } else {
          // Hall ticket number does not exist, insert a new record
          $insertQuery = "INSERT INTO sessional_marks (`Subject1 Name`, `Section`, `Session`, `Hall Ticket Number`, `Subject1`, `Sub1 Assignment`, `Sub1 Project`) VALUES (?, ?, ?, ?, ?, ?, ?)";
          $stmt = $conn->prepare($insertQuery);
          if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
          }
          $stmt->bind_param("sssssss", $subject, $section, $session, $hall_ticket, $marks, $assignment, $project);
        }
        if ($stmt->execute()) {
          echo "<p>Record successfully updated.</p>";
        } else {
          echo "<p>Error: " . $stmt->error . "</p>";
        }
    
        $stmt->close();
        $conn->close();
      }
    ?>
  </div>
</body>
</html>    
