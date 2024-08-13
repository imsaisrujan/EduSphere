<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Table</title>
    <link rel="icon" href="./time-table.png" type="image/icon type">
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
            width: 90vw;
            height: 86vh;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 17px 10px rgb(0 0 0 / 30%);
            border-radius: 20px;
            background: white;
            overflow: hidden;
            position: relative;
        }
        .table-container {
            width: 100%;
            height: 100%;
            overflow: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        th, td {
            padding: 5px; /* Adjust padding as necessary */
            border-bottom: 1px solid #ddd;
            max-width: 150px; /* Adjust as necessary */
            word-wrap: break-word;
            font-size: 12.3px; /* Adjust font size as necessary */
        }
        th {
            background-color: #f0f8ff;
        }
        .period-block {
            display: block;
        }
        .lunch-break {
            background-color: #f0f8ff; /* Gold color */
            font-weight: bold;
        }
        .day-column {
            background-color: #f0f8ff; /* Alice blue color */
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="table-container">
            <?php
            session_start();

            if (!isset($_SESSION['username'])) {
                header("Location: ../login.php");  // Adjust path if needed
                exit();
            }

            $username = $_SESSION['username'];  // Get the logged-in username
            // Database connection details
            $dsn = "mysql:host=localhost;dbname=student_db";
            $dbusername = "root";
            $dbpassword = "";

            try {
                // Create a PDO instance
                $pdo = new PDO($dsn, $dbusername, $dbpassword);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Query to fetch the section based on the username
                $sectionQuery = "SELECT section FROM students_login WHERE username = :username";
                $sectionStmt = $pdo->prepare($sectionQuery);
                $sectionStmt->bindParam(':username', $username, PDO::PARAM_STR);
                $sectionStmt->execute();

                if ($sectionStmt->rowCount() > 0) {
                    $sectionRow = $sectionStmt->fetch(PDO::FETCH_ASSOC);
                    $student_section = $sectionRow['section'];

                    // Query to fetch timetable data based on the student's section
                    $sql = "SELECT * FROM cse3_timetable WHERE section = :section";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':section', $student_section, PDO::PARAM_STR);
                    $stmt->execute();

                    // Initialize the variable
                    $lunchBreakDisplayed = false;

                    // Check if the query was successful
                    if ($stmt->rowCount() > 0) {
                        // Start building the timetable HTML
                        echo '<table>';
                        echo '<tr><th class="day-column">Day</th><th><p>Period 1</p>9:15 to 10:15</th><th><p>Period 2</p>10:15 to 11:15</th><th><p>Period 3</p>11:15 to 12:15</th><th class="lunch-break">12:15 to 13:05</th><th><p>Period 4</p>13:05 to 14:05</th><th><p>Period 5</p>14:05 to 15:05</th><th><p>Period 6</p>15:05 to 16:05</th></tr>';

                        // Loop through each row of the result set
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>';
                            echo '<td class="day-column">' . htmlspecialchars($row['Day']) . '</td>';
                            
                            // Display periods before lunch break
                            for ($i = 1; $i <= 3; $i++) {
                                $periodKey = 'Period' . $i;
                                $teacherKey = 'Teacher' . $i;

                                if (isset($row[$periodKey]) && isset($row[$teacherKey])) {
                                    $period = htmlspecialchars($row[$periodKey]);
                                    $teacher = htmlspecialchars($row[$teacherKey]);

                                    if (strpos($period, '/') !== false && strpos($teacher, '/') !== false) {
                                        list($period1, $period2) = explode('/', $period);
                                        list($teacher1, $teacher2) = explode('/', $teacher);
                                        echo '<td>';
                                        echo '<div class="period-block">' . $period1 . '<br>' . $teacher1 . '</div>' . '<hr/>';
                                        echo '<div class="period-block">' . $period2 . '<br>' . $teacher2 . '</div>';
                                        echo '</td>';
                                    } else {
                                        echo '<td>' . $period . '<br>' . $teacher . '</td>';
                                    }
                                } else {
                                    echo '<td></td>'; // Empty cell if period or teacher info is missing
                                }
                            }
                            
                            // Display lunch break
                            for ($i = 1; $i <= 6; $i++) {
                                // Display lunch break once per week
                                if ($i == 4 && !$lunchBreakDisplayed) {
                                    $lunchBreakDisplayed = true;
                                    echo '<td class="lunch-break" rowspan="6">Lunch Break</td>';
                                    continue;
                                }
                            }
                            
                            // Display periods after lunch break
                            for ($i = 4; $i <= 6; $i++) {
                                $periodKey = 'Period' . $i;
                                $teacherKey = 'Teacher' . $i;

                                if (isset($row[$periodKey]) && isset($row[$teacherKey])) {
                                    $period = htmlspecialchars($row[$periodKey]);
                                    $teacher = htmlspecialchars($row[$teacherKey]);

                                    if (strpos($period, '/') !== false && strpos($teacher, '/') !== false) {
                                        list($period1, $period2) = explode('/', $period);
                                        list($teacher1, $teacher2) = explode('/', $teacher);
                                        echo '<td>';
                                        echo '<div class="period-block">' . $period1 . '<br>' . $teacher1 . '</div>' . '<hr/>';
                                        echo '<div class="period-block">' . $period2 . '<br>' . $teacher2 . '</div>';
                                        echo '</td>';
                                    } else {
                                        echo '<td>' . $period . '<br>' . $teacher . '</td>';
                                    }
                                } else {
                                    echo '<td></td>'; // Empty cell if period or teacher info is missing
                                }
                            }
                            
                            echo '</tr>';
                        }
                        echo '</table>';
                    } else {
                        echo "No timetable data found for the selected section.";
                    }
                } else {
                    echo "Section not found for the logged-in user.";
                }
            } catch (PDOException $e) {
                // Handle connection error
                echo "Connection failed: " . $e->getMessage();
                exit();
            }

            // Close the database connection
            $pdo = null;
            ?>
        </div>
    </div>
</body>
</html>