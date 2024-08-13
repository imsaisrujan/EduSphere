<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$loggedInUsername = $_SESSION['username'];
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

if (isset($_GET['sem'])) {
    $sem = $_GET['sem'];
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Marks</title>
    <style>
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
        .container h1 {
            margin-bottom: 20px;
        }
        .buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .buttons form {
            margin: 0;
        }
        .buttons button {
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color:#2c7873;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th{
          background-color: #4e8975;
          color:white;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Final Marks</h1>
    <div class="buttons">
        <?php foreach ($semesters as $sem): ?>
            <form method="GET" action="">
                <input type="hidden" name="sem" value="<?php echo $sem; ?>">
                <button type="submit"><?php echo "Semester $sem"; ?></button>
            </form>
        <?php endforeach; ?>
    </div>
    <?php if (isset($subjects)): ?>
        <table>
            <tr>
                <th>S.No</th>
                <th>Subject</th>
                <th>Credits</th>
                <th>Grade</th>
                <th>Points Secured</th>
            </tr>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?php echo $subject['sno']; ?></td>
                    <td><?php echo $subject['subject']; ?></td>
                    <td><?php echo $subject['credits']; ?></td>
                    <td><?php echo $subject['grade']; ?></td>
                    <td><?php echo $subject['pointsSecured']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (isset($sgpa)): ?>
            <h2>SGPA: <?php echo number_format($sgpa, 2); ?></h2>
        <?php else: ?>
            <h2>SGPA: Not Available (F grade in one or more subjects)</h2>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>

<?php $conn->close(); ?>