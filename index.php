<?php
include('includes/dp.php'); 
session_start();

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($username) && !empty($password)) {

        // Check if username exists in users table

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];

                // Store login details in 'logins' table
                $stmt = $conn->prepare("INSERT INTO logins (user_id, username, password) VALUES (?, ?, ?)");
                $stmt->execute([$user['user_id'], $username, $user['password']]);

                // Redirect to homepage
                if ($user['role'] === 'admin') {
                    header("Location: admin.html");
                }
                else {
                header("Location: homepage.html");
                }
                exit();
            } else {
                $error_message = "❌ Incorrect password. Please try again.";
            }
        } else {
            $error_message = "❌ Username not registered. Please Register first.";
        }
    }
}
?>