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
  $stmt = $pdo->prepare("SELECT * FROM parents_login WHERE username = :username");
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    // Verify password using direct comparison (for debugging only)
    if ($password === $user['password']) {
      // Login successful, start session
      session_start();
      $_SESSION['user_id'] = $user['id']; // Store user ID in session

      // Shorten username to a maximum of 10 characters
      $shortUsername = substr($username, 0, 10);
      $_SESSION['username'] = $shortUsername; // Store shortened username in session
      $_SESSION['hallticketnumber'] = $user['hallticketnumber']; // Store hall ticket number in session

      // Redirect to teacher main page
      header("Location: ./ParentMainPage.html");
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
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Login</button>
  </form>
  <?php if (isset($error)) { ?>
    <p style="color: red;"><?php echo $error; ?></p>
  <?php } ?>
</body>
</html>