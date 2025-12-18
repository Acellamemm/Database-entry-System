<?php
session_start();
include("connection.php"); // Your DB connection

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, fname, lname, dj_alias, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verify password (assuming you used password_hash() on registration)
            if (password_verify($password, $user['password'])) {
                // Success! Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fname'] = $user['fname'];
                $_SESSION['lname'] = $user['lname'];
                $_SESSION['dj_alias'] = $user['dj_alias'] ?: $user['fname'];
                $_SESSION['email'] = $email;

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Error - FusionMix</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-container {
            text-align: center;
            padding: 40px;
            color: #ff6b6b;
        }
        .error-container h2 {
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        .error-list {
            list-style: none;
            margin: 20px 0;
        }
        .error-list li {
            margin: 10px 0;
            font-size: 1.1rem;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <div class="brand">
                <div class="logo"><div class="badge">🎧</div><div>FusionMix</div></div>
            </div>
            <div class="subtitle">Oops — something went wrong</div>
            <div class="deck-art">
                <div class="eq">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="error-container">
                <h2>Login Failed</h2>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="login.php" class="back-link">Try Again</a>
            </div>
        </div>
    </div>
</body>
</html>