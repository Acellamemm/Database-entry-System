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
            </form>
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
