<?php
// Database connection details (replace with your actual credentials)
$dsn = "mysql:host=localhost;dbname=student_db";
$dbusername = "root";
$dbpassword = "";

// Define cost factor for password hashing (adjust as needed)
$costFactor = 12;

try {
  $pdo = new PDO($dsn, $dbusername, $dbpassword);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  // Validate username (optional)
  // You can add username validation here (e.g., length, allowed characters)

  // Attempt to retrieve user by username
  $stmt = $pdo->prepare("SELECT * FROM students_login WHERE username = :username");
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    // Debug: Output fetched user and password for troubleshooting
    var_dump($user);
    var_dump($password);

    // Verify password using direct comparison (for debugging only)
    if ($password === $user['password']) {
      // Debug: Password matches
      echo "Password matches.";

      // Login successful, start session
      session_start();
      $_SESSION['user_id'] = $user['id']; // Store user ID in session
      $_SESSION['username'] = $username; // Store username in session (optional)
      
      // Redirect to student main page
      header("Location: ./StudentMainPage.html");
      exit();
    } else {
      $error = "Incorrect password.";
    }
  } else {
    $error = "Username not found.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
  <?php if(isset($error)) { ?>
    <p style="color: red;"><?php echo $error; ?></p>
  <?php } ?>
</body>
</html>
