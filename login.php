<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();
        
        // Verify the password
        if (hash('sha256', $password) === $hashed_password) {
            // Store user data in session
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            header('Location: welcome.php');
            exit();
        } else {
            echo 'Invalid username or password!';
        }
    } else {
        echo 'Invalid username or password!';
    }

    $stmt->close();
    $conn->close();
}
?>
