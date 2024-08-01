<?php
// Start the session
session_start();

// Define database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "login_system";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $resume = $_FILES['resume'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($resume["name"]);
    $upload_ok = 1;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a valid document type
    $valid_extensions = array("pdf", "doc", "docx");
    if (!in_array($file_type, $valid_extensions)) {
        echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
        $upload_ok = 0;
    }

    // Check if $upload_ok is set to 0 by an error
    if ($upload_ok == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Save the file and record data in the database
        if (move_uploaded_file($resume["tmp_name"], $target_file)) {
            // Prepare and execute the query to save the data
            $stmt = $conn->prepare("INSERT INTO submissions (name, email, phone, resume_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $target_file);
            $stmt->execute();
            $stmt->close();
            
            echo "The file ". htmlspecialchars(basename($resume["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Close the database connection
$conn->close();
?>
