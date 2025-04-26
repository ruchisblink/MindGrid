<?php
session_start();

// Database connection settings
$host = 'localhost';
$dbname = 'mindgrid'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in both fields.']);
        exit;
    }

    // Prepare the SQL query to check if the user exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and the password matches
    if ($user && password_verify($password, $user['password'])) {
        // Start a session for the user
        $_SESSION['user_id'] = $user['id']; // Save user ID in session
        $_SESSION['email'] = $user['email']; // Save email in session

        // Save the user's last visited page in session
        $lastPage = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : 'welcome.php';

        // Return success and the last visited page
        echo json_encode(['success' => true, 'user' => $user, 'redirect_url' => $lastPage]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials, please try again.']);
    }
}
?>
