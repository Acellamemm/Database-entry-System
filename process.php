<?php
include("connection.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
        $fname = filter_input(INPUT_POST,"fname", FILTER_SANITIZE_SPECIAL_CHARS);
        $lname = filter_input(INPUT_POST,"lname", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST,"email", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST,"password", FILTER_SANITIZE_SPECIAL_CHARS);

        if(empty($fname)){
            echo"Please enter firstname";
        }
        elseif(empty($lname)){
            echo"Please enter lastname";
        }elseif(empty($email)){
            echo"Please enter email address";
        }elseif(empty($password)){
            echo"Please enter password";
        }
        else{
            $hash =password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (firstname,lastname,email,password) 
                    values('$fname','$lname','$email','$password')";
               mysqli_query($conn, $sql);
               echo"User is now registred";
            header("Location: index.php");
             

            
        }
}
