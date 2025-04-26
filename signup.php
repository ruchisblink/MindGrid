<?php
// Database connection settings
$host = "localhost"; // Replace with your host if not localhost
$db_user = "root"; // Default username for XAMPP is 'root'
$db_pass = ""; // Default password for XAMPP is empty
$db_name = "mindgrid_db"; // Replace with your database name

// Establishing the database connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Getting form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST["name"]));
    $email = $conn->real_escape_string(trim($_POST["email"]));
    $password = $conn->real_escape_string(trim($_POST["password"]));

    // Check for duplicate emails
    $checkDuplicate = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkDuplicate);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use a different email.'); window.location.href='signup.html';</script>";
    } else {
        // Inserting data into database
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hashing the password
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Account created successfully. You can now sign in.'); window.location.href='signin.html';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
