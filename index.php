<?php
include("connection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>
    <h2>Welcome to Fakebook!</h2>
    <form action="process.php" method="post">
    First name <br>
    <input type="text" id="fname" name="fname" placeholder="Enter first name" ><br>
    Last name <br>
    <input type="text" id="lname" name="lname" placeholder="Enter last name" ><br>
    Email <br>
    <input type="email" name="email" placeholder="nate7722@proton.me"><br>
    Password <br>
    <input type="password" name="password" placeholder="MY#@126666!" minlength=8 ><br>
    <input type="submit" name="submit" value="Register">
    </form>
    
</body>
</html>

<?php
mysqli_close($conn);
?>
