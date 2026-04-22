<?php
include('db-connection.php'); 
session_start();

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];

                date_default_timezone_set("Asia/Kolkata");
                $current_time = date("Y-m-d H:i:s");

                $stmt = $conn->prepare("INSERT INTO logins (user_id, username, password, created_at) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['user_id'], $username, $user['password'], $current_time]);

                if ($user['role'] === 'admin') {
                    header("Location: ../frontend/admin.html");
                } else {
                    header("Location: ../frontend/homepage.html");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Travel Booking</title>
    <style>
        /* ================================
           AEROPRIME PROFESSIONAL THEME
           ================================ */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", "Poppins", sans-serif;
        }

        /* Navbar */
        nav {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #1e3a8a; /* Deep professional navy */
            color: white;
            padding: 18px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        nav .logo {
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Body */
        body {
            background: linear-gradient(135deg, #f8fafc, #e5ecff);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 80px; /* Space for navbar */
            color: #1e1e1e;
        }

        /* Login Card */
        form div {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.08);
            padding: 40px 45px;
            width: 400px;
            animation: fadeUp 0.7s ease-in-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Heading */
        h1 {
            font-size: 32px;
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* Input Fields */
        #username,
        #password {
            height: 45px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f9fafb;
            color: #1e1e1e;
            padding: 0 15px;
            font-size: 15px;
            margin-bottom: 20px;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        #username:focus,
        #password:focus {
            border-color: #3b82f6;
            background-color: #ffffff;
            outline: none;
        }

        ::placeholder {
            color: #64748b;
            opacity: 0.8;
        }

        /* Checkbox */
        input[type="checkbox"] {
            accent-color: #1e3a8a;
            margin-right: 5px;
            cursor: pointer;
        }

        /* Submit Button */
        #submit {
            height: 45px;
            width: 100%;
            border-radius: 8px;
            background-color: #1e3a8a;
            color: #ffffff;
            border: none;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #submit:hover {
            background-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        /* Links */
        p {
            text-align: center;
            margin: 20px 0 10px;
            color: #475569;
            font-size: 14px;
        }

        p a {
            color: #1e3a8a;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        p a:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        /* Error Message */
        .error-message {
            color: #dc2626;
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            form div {
                width: 90%;
                padding: 30px 25px;
            }

            nav .logo {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav>
        <div class="logo">Travel Booking</div>
    </nav>

    <!-- Login Form -->
    <form action="index.php" method="post">
        <div>
            <h1>Login</h1>
            <input type="text" placeholder="Username" id="username" name="username" required><br>
            <input type="password" placeholder="Password" id="password" name="password" required><br>

            <label>
                <input type="checkbox" name="remember_me"> Remember me
            </label><br><br>

            <input type="submit" value="Login" id="submit"><br>

            <p>Don't have an account? <a href="register.php" target="_main">Register</a></p>

            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>
    </form>

</body>
</html>
