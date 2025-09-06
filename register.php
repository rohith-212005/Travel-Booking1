<?php
include('includes/dp.php');
session_start();

if (isset($_POST['register'])) {
    $name = trim($_POST['fullname']);
    $email = $_POST['email'];
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $error_message = "Email is already registered!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, username, phone, gender, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $gender, $dob, $hashed_password]);
            $_SESSION['user_id'] = $conn->lastInsertId();
            header("Location: homepage.php");
            exit();
        }
    }
}
?>