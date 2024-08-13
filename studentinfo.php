<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Info</title>
    <link rel="icon" href="./studentinfo.png" type="image/icon type">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        *, html, body {
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
            width: 80vw;
            height: 70vh;
            padding: 20px;
            box-shadow: 0 0 17px 10px rgb(0 0 0 / 30%);
            border-radius: 20px;
            background: white;
            overflow: hidden;
            position: relative;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            grid-gap: 20px;
        }
        .student-info {
            background-color: #f5f5f5;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            grid-column: 1 ;
            grid-row: 1 / 4;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width : 600px;
        }
        .attendance-record {
            background-color: #f5f5f5;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            grid-column: 3 ;
            grid-row: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width:500px;
            height : 180px;
        }
        .academic-record {
            background-color: #f5f5f5;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            grid-column: 3 ;
            grid-row: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height :180px;
        }
        .student-info h2, .attendance-record h2, .academic-record h2 {
            margin-top: 0;
            font-size: 24px;
            font-weight: 600;
            color: #333;
            text-align: center;
        }
        .student-info p, .attendance-record p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        .student-info strong, .attendance-record strong {
            font-weight: 600;
            color: #333;
        }
    
        .academic-record p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .academic-record p span {
            width: 80%;
            height: 20px;
            background-color: #ddd;
            border-radius: 10px;
            position: relative;
        }
        .academic-record p span::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            border-radius: 10px;
            background-color: #4CAF50;
        }
        .academic-record p:first-child span::before {
            width: 90%;
        }
        .academic-record p:last-child span::before {
            width: 90%;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="student-info">
            <h2>Student Information</h2><br/>
            <?php
              session_start();

              // Check if the username is set in the session
              if(isset($_SESSION['username'])) {
                  $loggedInUsername = $_SESSION['username'];
              } else {
                  echo "Username not set in session.";
              }
              $dsn = "mysql:host=localhost;dbname=student_db";
              $dbusername = "root";
              $dbpassword = "";
              try {
                  $pdo = new PDO($dsn, $dbusername, $dbpassword);
                  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              } catch(PDOException $e) {
                  echo "Connection failed: " . $e->getMessage();
                  exit();
              }

              // Assuming you have a session variable named 'username' that holds the logged-in username
              $loggedInUsername = $_SESSION['username'];

              // Query to retrieve data from your table based on the logged-in username
              $sql = "SELECT username AS 'Hall ticket No', `Student Name`, Section, Address, `Phone Number`, `Email Id`, `Father Name`, `Father Phone Number`, `Father Email Id`, `Mother Name`, `Mother Phone Number`,'Image'
                      FROM students_login
                      WHERE username = :username";
              $stmt = $pdo->prepare($sql);
              $stmt->bindParam(':username', $loggedInUsername);
              $stmt->execute();
              if ($stmt->rowCount() > 0) {
                  // Output data of each row
                  $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  $imageData = $row['Image'];

                  echo "<p><strong>Hall ticket No:</strong> ".$row["Hall ticket No"]."</p>";
                  echo "<p><strong>Student Name:</strong> ".$row["Student Name"]."</p>";
                  echo "<p><strong>Section:</strong> ".$row["Section"]."</p>";
                  echo "<p><strong>Address:</strong> ".$row["Address"]."</p>";
                  echo "<p><strong>Phone Number:</strong> ".$row["Phone Number"]."</p>";
                  echo "<p><strong>Email Id:</strong> ".$row["Email Id"]."</p>";
                  echo "<p><strong>Father Name:</strong> ".$row["Father Name"]."</p>";
                  echo "<p><strong>Father Phone Number:</strong> ".$row["Father Phone Number"]."</p>";
                  echo "<p><strong>Father Email Id:</strong> ".$row["Father Email Id"]."</p>";
                  echo "<p><strong>Mother Name:</strong> ".$row["Mother Name"]."</p>";
                  echo "<p><strong>Mother Phone Number:</strong> ".$row["Mother Phone Number"]."</p>";
              } else {
                  echo "0 results";
              }
              ?>
        </div>
        <div class="attendance-record">
    <h2>Attendance Detail</h2>
    <?php
    // Fetch student attendance data with aliases and COALESCE
    $servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";

// Create connection with error handling
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    $sql = <<<SQL
    SELECT 
      COUNT(`Business Economics and Financial Analysis`) as business_economics_held,
      SUM(`Business Economics and Financial Analysis`) as business_economics,
      COUNT(`Constitution of India`) as constitution_held,
      SUM(`Constitution of India`) as constitution,
      COUNT(`Database Management Systems`) as database_systems_held,
      SUM(`Database Management Systems`) as database_systems,
      COUNT(`Database Management Systems Lab`) as database_systems_lab_held,
      SUM(`Database Management Systems Lab`) as database_systems_lab,
      COUNT(`Discrete Mathematics`) as discrete_mathematics_held,
      SUM(`Discrete Mathematics`) as discrete_mathematics,
      COUNT(`Node JS Lab`) as node_js_lab_held,
      SUM(`Node JS Lab`) as node_js_lab,
      COUNT(`Operating Systems`) as operating_systems_held,
      SUM(`Operating Systems`) as operating_systems,
      COUNT(`Operating Systems Lab`) as operating_systems_lab_held,
      SUM(`Operating Systems Lab`) as operating_systems_lab,
      COUNT(`RT Project`) as rt_project_held,
      SUM(`RT Project`) as rt_project,
      COUNT(`Software Engineering`) as software_engineering_held,
      SUM(`Software Engineering`) as software_engineering,
      COUNT(`Training & Placements`) as training_placements_held,
      SUM(`Training & Placements`) as training_placements
    FROM student_attendance 
    WHERE rollno = ?
    SQL;
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Failed to prepare statement: " . $conn->error);
    }
    $stmt->bind_param("s", $loggedInUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    $studentAttendance = $result->fetch_assoc();

    // Map full subject names to column aliases
    $subjectKey = [
        'Business Economics and Financial Analysis' => ['held' => 'business_economics_held', 'attended' => 'business_economics'],
        'Constitution of India' => ['held' => 'constitution_held', 'attended' => 'constitution'],
        'Database Management Systems' => ['held' => 'database_systems_held', 'attended' => 'database_systems'],
        'Database Management Systems Lab' => ['held' => 'database_systems_lab_held', 'attended' => 'database_systems_lab'],
        'Discrete Mathematics' => ['held' => 'discrete_mathematics_held', 'attended' => 'discrete_mathematics'],
        'Node JS Lab' => ['held' => 'node_js_lab_held', 'attended' => 'node_js_lab'],
        'Operating Systems' => ['held' => 'operating_systems_held', 'attended' => 'operating_systems'],
        'Operating Systems Lab' => ['held' => 'operating_systems_lab_held', 'attended' => 'operating_systems_lab'],
        'RT Project' => ['held' => 'rt_project_held', 'attended' => 'rt_project'],
        'Software Engineering' => ['held' => 'software_engineering_held', 'attended' => 'software_engineering'],
        'Training & Placements' => ['held' => 'training_placements_held', 'attended' => 'training_placements']
    ];

    // Calculate total classes held and attended
    $totalHeld = 0;
    $totalAttended = 0;

    foreach ($subjectKey as $subject => $key) {
        $classesHeld = $studentAttendance[$key['held']] ?? 0;
        $classesAttended = $studentAttendance[$key['attended']] ?? 0;

        $totalHeld += $classesHeld;
        $totalAttended += $classesAttended;
    }

    // Calculate overall attendance percentage
    $overallAttendancePercentage = $totalHeld !== 0 ? round(($totalAttended / $totalHeld) * 100, 2) : 0;

    echo "<p><strong>Total Attendance:</strong> <span class='percentage " . ($overallAttendancePercentage >= 75 ? 'good' : ($overallAttendancePercentage >= 60 ? 'okay' : 'poor')) . "'>" . $overallAttendancePercentage . "%</span></p>";
    ?>
</div>
            <div class="academic-record">
            <h2>Past Academic Record</h2><br>
<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "student_db";
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Query to retrieve SGPA and CGPA data based on the logged-in username
    $query = "SELECT DISTINCT SEM FROM final_marks";

    $result = $conn->query($query);

    $semesters = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $semesters[] = $row['SEM'];
        }
    }

    function getPoints($grade) {
        switch ($grade) {
            case 'O': return 10;
            case 'A+': return 9;
            case 'A': return 8;
            case 'B+': return 7;
            case 'B': return 6;
            case 'C': return 5;
            case 'F': return 0;
            default: return 0;
        }
    }

    $totalSGPA = 0;
    $totalSemesters = 0;

    foreach ($semesters as $sem) {
        $query = "SELECT * FROM final_marks WHERE SEM = ? AND `Hall Ticket Number` = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error in preparing statement: " . $conn->error);
        }
        $stmt->bind_param("ss", $sem, $loggedInUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            die("Error executing query: " . $stmt->error);
        }

        $row = $result->fetch_assoc();

        $subjects = [];
        $totalCredits = 0;
        $totalPoints = 0;
        $hasFGrade = false;

        for ($i = 1; $i <= 10; $i++) {
            if (!empty($row["Subject$i"])) {
                $subject = $row["Subject$i"];
                $credits = $row["Credits$i"];
                $grade = $row["Grade$i"];
                $pointsSecured = getPoints($grade) * $credits;

                $subjects[] = [
                    'sno' => $i,
                    'subject' => $subject,
                    'credits' => $credits,
                    'grade' => $grade,
                    'pointsSecured' => $pointsSecured
                ];

                $totalCredits += $credits;
                $totalPoints += $pointsSecured;

                if ($grade == 'F') {
                    $hasFGrade = true;
                }
            }
        }

        if ($totalCredits > 0 && !$hasFGrade) {
            $sgpa = $totalPoints / $totalCredits;
            echo "<p><strong>Semester $sem SGPA:</strong> " . number_format($sgpa, 2) . "</p>";
            $totalSGPA += $sgpa;
            $totalSemesters++;
        } else {
            echo "<p><strong>Semester $sem SGPA:</strong> Not Available (F grade in one or more subjects)</p>";
        }
    }

    if ($totalSemesters > 0) {
        $cgpa = $totalSGPA / $totalSemesters;
        echo "<p><strong>CGPA:</strong> " . number_format($cgpa, 2) . "</p>";
    } else {
        echo "<p><strong>CGPA:</strong> Not Available (F grade in one or more subjects)</p>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
</div>
</div>
</body>
</html>