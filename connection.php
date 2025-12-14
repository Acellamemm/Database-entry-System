<?php
//code for connecting to the database

        $host ="localhost";
        $db_user ="root";
        $db_password ="Pronunciation";
        $db_name = "registration";
        $conn ="";
        
        $conn = mysqli_connect($host, $db_user, $db_password, $db_name);
?>
