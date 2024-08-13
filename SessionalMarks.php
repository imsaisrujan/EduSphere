<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");  // Adjust path if needed
    exit();
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loggedInUsername = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessional Marks</title>
    <link rel="icon" href="./sessionalmarks.png" type="image/icon type">
    <style>
        /* CSS styles remain unchanged */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom right, #1a4e64, #2c7873, #4e8975);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90vw;
            height: 80vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            box-shadow: 0 0 17px 10px rgb(0 0 0 / 30%);
            border-radius: 20px;
            background: white;
            overflow-y: auto;
            position: relative;
        }
        .heading { text-align: center; font-size: 28px; font-weight: bold; margin-bottom: 20px; }
        .subject-columns { display: flex; width: 100%; justify-content: space-between; height: calc(100% - 60px); }
        .subject-list { width: 45%; overflow-y: auto; padding-right: 10px; }
        .subject { cursor: pointer; padding: 10px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px; }
        .marks { display: none; padding: 10px; background: #e0e0e0; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="heading">Sessional Marks</div><br/>
    <div class="subject-columns">
        <div class="subject-list" id="left-column">
            <?php
            $sql = "SELECT * FROM sessional_marks WHERE `Hall Ticket Number` = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Failed to prepare statement: " . $conn->error);
            }
            $stmt->bind_param("s", $loggedInUsername);
            $stmt->execute();
            $result = $stmt->get_result();

            $subjects = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    for ($i = 1; $i <= 10; $i++) {
                        $subject_name = $row["Subject{$i} Name"];
                        if (!empty($subject_name)) {
                            $subject = $row["Subject{$i}"];
                            $assignment_marks = $row["Sub{$i} Assignment"];
                            $project_marks = $row["Sub{$i} Project"];
                            $session = $row["Session"];

                            if (!isset($subjects[$subject_name])) {
                                $subjects[$subject_name] = [];
                            }

                            $subjects[$subject_name][] = [
                                'session' => $session,
                                'subject' => $subject,
                                'assignment_marks' => $assignment_marks,
                                'project_marks' => $project_marks
                            ];
                        }
                    }
                }

                $subjectCount = 0;
                foreach ($subjects as $subject_name => $sessions) {
                    if ($subjectCount < 5) {
                        echo "<div class='subject' onclick='toggleMarks(this)'>$subject_name</div>";
                        echo "<div class='marks'>";
                        foreach ($sessions as $session_data) {
                            $session = $session_data['session'];
                            $subject = $session_data['subject'];
                            $assignment_marks = $session_data['assignment_marks'];
                            $project_marks = $session_data['project_marks'];

                            echo "Session: $session<br>";
                            if (empty($subject)) {
                                echo "Examination Marks: Marks Need to be Updated<br>";
                            } else {
                                echo "Examination Marks: $subject<br>";
                            }

                            if (empty($assignment_marks)) {
                                echo "Assignment Marks: Marks Need to be Updated<br>";
                            } else {
                                echo "Assignment Marks: $assignment_marks<br>";
                            }

                            if (empty($project_marks)) {
                                echo "Project Marks: Marks Need to be Updated<br>";
                            } else {
                                echo "Project Marks: $project_marks<br>";
                            }

                            if (!empty($subject) || !empty($assignment_marks) || !empty($project_marks)) {
                                $total_marks = intval($subject) + intval($assignment_marks) + intval($project_marks);
                                echo "Total Marks: $total_marks / 40<br>";
                            }
                        }
                        echo "</div>";
                    }
                    $subjectCount++;
                }
            } else {
                echo "No marks available.";
            }
            ?>
        </div>
        <div class="subject-list" id="right-column">
            <?php
            $subjectCount = 0;
            foreach ($subjects as $subject_name => $sessions) {
                if ($subjectCount >= 5) {
                    echo "<div class='subject' onclick='toggleMarks(this)'>$subject_name</div>";
                    echo "<div class='marks'>";
                    foreach ($sessions as $session_data) {
                        $session = $session_data['session'];
                        $subject = $session_data['subject'];
                        $assignment_marks = $session_data['assignment_marks'];
                        $project_marks = $session_data['project_marks'];

                        echo "Session: $session<br>";
                        if (empty($subject)) {
                            echo "Examination Marks: Marks Need to be Updated<br>";
                        } else {
                            echo "Examination Marks: $subject<br>";
                        }

                        if (empty($assignment_marks)) {
                            echo "Assignment Marks: Marks Need to be Updated<br>";
                        } else {
                            echo "Assignment Marks: $assignment_marks<br>";
                        }

                        if (empty($project_marks)) {
                            echo "Project Marks: Marks Need to be Updated<br>";
                        } else {
                            echo "Project Marks: $project_marks<br>";
                        }

                        if (!empty($subject) || !empty($assignment_marks) || !empty($project_marks)) {
                            $total_marks = intval($subject) + intval($assignment_marks) + intval($project_marks);
                            echo "Total Marks: $total_marks / 40<br>";
                        }
                    }
                    echo "</div>";
                }
                $subjectCount++;
            }
            ?>
        </div>
    </div>
</div>

<script>
    function toggleMarks(element) {
        const marksDiv = element.nextElementSibling;
        if (marksDiv.style.display === "none" || marksDiv.style.display === "") {
            marksDiv.style.display = "block";
        } else {
            marksDiv.style.display = "none";
        }
    }
</script>
</body>
</html>

<?php $conn->close(); ?>