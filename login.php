<?php
include("connection.php");
session_start(); // For login sessions later
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FusionMix - DJ Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Tiny DJ enhancements that play nice with your existing CSS */
        .eq span {
            box-shadow: 0 0 12px rgba(255, 65, 108, 0.6); /* Soft neon glow on bars */
        }

        input:focus, select:focus {
            border-color: var(--accent1);
            box-shadow: 0 0 15px rgba(255, 65, 108, 0.3);
        }

        .submit-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 25px rgba(255, 75, 43, 0.4);
        }

        .links {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #cbd5e1;
        }

        .links a {
            color: var(--accent1);
            text-decoration: none;
            font-weight: 600;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <div class="brand">
                <div class="logo">
                    <div class="badge">🎧</div>
                    <div>FusionMix</div>
                </div>
            </div>
            <div class="subtitle">Welcome back. Log in to drop new mixes, connect with the community, and own the decks.</div>
            <div class="bg-wrapper">
            <im src="" alt="" class="bg-image">
            <div class="bg-overlay"></div>
            </div>
            <div class="deck-art">
                <div class="eq" aria-hidden="true">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
                <div class="note">Be the first to drop your beats.</div>
            </div>
        </div>
        <div class="right">
            <form action="login_process.php" method="post">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit" class="submit-btn">Log In as DJ</button>
            </form>

            <div class="links">
                <a href="forgot_password.php">Forgot password?</a><br><br>
                No account yet? <a href="index.php">Create DJ Account</a><br>
                Not Ready to sign up? <a href="public_feed.php">Browse Mixes without logging in</a>

            </div>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>