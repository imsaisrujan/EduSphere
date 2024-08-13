<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Subject Details/Syllabus</title>
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
    <h1>View Subject Details/Syllabus</h1>
    <form method="post">
      <div class="dropdown-container">
        <label for="subject-dropdown">Select Subject:</label>
        <select name="subject" id="subject-dropdown">
          <option value="">Select Subject</option>
          <?php
            // Enable error reporting
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

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
        <label for="section-dropdown">Select Regulation:</label>
        <select name="section" id="section-dropdown">
          <option value="">Select Regulation</option>
          <?php
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch regulations from the subject_details table
            $regulationsQuery = "SELECT DISTINCT `Regulation` FROM subject_details";
            $regulationsResult = $conn->query($regulationsQuery);

            if ($regulationsResult) {
                while ($row = $regulationsResult->fetch_assoc()) {
                    $regulation = $row["Regulation"];
                    if (!empty($regulation)) {
                        echo "<option value='$regulation'>$regulation</option>";
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
      <button type="submit">Submit</button>
    </form>
    <?php
      if (isset($_POST['subject']) && isset($_POST['section'])) {
        $subject = $_POST['subject'];
        $regulation = $_POST['section'];

        echo "Subject: $subject<br>";
        echo "Regulation: $regulation<br>";

        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'student_db'); // Replace with your actual database credentials

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch syllabus from the subject_details table
        $syllabusQuery = "SELECT `Syllabus` FROM subject_details WHERE `Subject Name`='$subject' AND `Regulation`='$regulation'";
        $syllabusResult = $conn->query($syllabusQuery);

        // After fetching syllabus blob from the database
        if ($syllabusResult && $syllabusResult->num_rows > 0) {
            $row = $syllabusResult->fetch_assoc();
            $syllabus_blob = $row['Syllabus'];

            // Generate download link for syllabus PDF
            $base64_pdf = base64_encode($syllabus_blob); // Convert the blob data to base64
            echo "<a href='data:application/pdf;base64,$base64_pdf' download='syllabus.pdf'>Download Syllabus PDF</a>";
        } else {
            echo "<p>No syllabus found for the selected subject and regulation.</p>";
        }

        // Close the connection
        $conn->close();
      }
    ?>
  </div>
</body>
</html>
