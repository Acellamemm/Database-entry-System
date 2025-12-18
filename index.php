<?php
include("connection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FusionMix - DJ Registration</title>
    <style>
    </style>
    <link rel="stylesheet" href="style.css"></link>
    <style>
        /* Fix dropdown readability */
            select, .modal select {
                background: rgba(30, 30, 60, 0.9) !important; /* Dark background */
                color: white !important; /* White text */
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            select option {
                background: #0f0c29; /* Dark option background */
                color: white;
            }

            .modal select {
                appearance: none;
                -webkit-appearance: none;
                padding-right: 30px; /* Space for arrow */
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1 0l5 6 5-6z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
            }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <div class="brand">
                <div class="logo"><div class="badge">🎧</div><div>FusionMix</div></div>
            </div>
            <div class="subtitle">Spin. Mix. Perform. Join the community of DJs and producers.</div>
            <div class="deck-art">
                <div class="eq" aria-hidden="true">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
                <div class="note">Share your DJ alias and genres to connect with other artists.</div>
            </div>
        </div>
        <div class="right">
            <form action="process.php" method="post" id="registrationForm">
                <div class="row">
                    <div>
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" placeholder="Acellam" required>
                    </div>
                    <div>
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" placeholder="Emmanuel" required>
                    </div>
                </div>
                <label for="dj_alias">DJ Alias</label>
                <input type="text" id="dj_alias" name="dj_alias" placeholder="DJ Echo (optional)">
                <label for="genre">Primary Genre</label>
                <select id="genre" name="genre" required>
                    <option value="">Select genre</option>
                    <option>House</option>
                    <option>Techno</option>
                    <option>Drum & Bass</option>
                    <option>Hip Hop</option>
                    <option>Dubstep</option>
                    <option>Trap</option>
                    <option>PoP</option>
                    <option>Dubstep</option>
                    <option>Amapiano</option>
                    <option>Afrobeat</option>
                    <option>Chill</option>
                    <option>Other</option>
                </select>
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
                <label for="equipment">Main Equipment</label>
                <input type="text" id="equipment" name="equipment" placeholder="CDJs, Controller, Vinyl, etc. (optional)">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="At least 8 characters" minlength="8" required>
                <div class="password-strength" id="passwordStrength"></div>
                <button type="submit" class="submit-btn" name="submit">Create DJ Account</button>
            </form><br>Have an account? <a href="login.php" style="color: blue;">Log in here</a>
        </div>
    </div>
    <script>
        // Password strength indicator (same logic, updated visuals)
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('passwordStrength');
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let message = '';
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            if (password.length === 0) {
                strengthIndicator.className = 'password-strength';
                strengthIndicator.style.display = 'none';
            } else if (strength <= 2) {
                strengthIndicator.className = 'password-strength weak';
                message = '⚠️ Weak password — add numbers, symbols, and mixed case';
            } else if (strength === 3) {
                strengthIndicator.className = 'password-strength medium';
                message = '✓ Medium strength — almost there';
            } else {
                strengthIndicator.className = 'password-strength strong';
                message = '✓ Strong password — good to go';
            }
            strengthIndicator.textContent = message;
            strengthIndicator.style.display = message ? 'block' : 'none';
        });
        // Simple client-side validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const fname = document.getElementById('fname').value.trim();
            const lname = document.getElementById('lname').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            if (!fname || !lname || !email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
