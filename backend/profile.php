<?php
include 'db-connection.php';
session_start();

// ✅ Get user_id from session
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT name, username, gender, dob, phone 
                            FROM users WHERE user_id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $user = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../styles/style.css"> <!-- ✅ Use homepage theme -->
    <style>
        .profile-container {
            width: 500px;
            margin: 80px auto;
            background: #fff;
            padding: 40px 20px;
            border-radius: 15px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
            text-align: center;
        }
        .profile-logo {
            width: 150px;
            height: 150px;
            border-radius: 50%; /* ✅ round */
            margin-bottom: 20px;
            border: 4px solid #007bff;
            object-fit: cover; /* ✅ keeps image inside circle */
        }
        h2 {
            margin-bottom: 20px;
            color: #222;
            font-size: 26px;
        }
        p {
            font-size: 17px;
            margin: 10px 0;
            color: #555;
        }
        .highlight {
            font-weight: bold;
            color: #111;
        }
    </style>
</head>
<body>


<div class="profile-container">
    <!-- Profile Logo -->
    <img src="https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740&q=80" alt="Profile Logo" class="profile-logo">

    <h2>User Profile</h2>

    <?php if ($user): ?>
        <p><span class="highlight">Name:</span> <?= htmlspecialchars($user['name']) ?></p>
        <p><span class="highlight">Email/Username:</span> <?= htmlspecialchars($user['username']) ?></p>
        <p><span class="highlight">Gender:</span> <?= htmlspecialchars($user['gender']) ?></p>
        <p><span class="highlight">DOB:</span> <?= htmlspecialchars($user['dob']) ?></p>
        <p><span class="highlight">Phone:</span> <?= htmlspecialchars($user['phone']) ?></p>
        
    <?php else: ?>
        <p>You must log in to see your profile.</p>
    <?php endif; ?>
</div>

</body>
</html>
