<?php   
  $con = mysqli_connect("localhost","root","","ass1_competition");
  if (mysqli_connect_errno())
    {
      die("Failed to connect to MySQL: " . mysqli_connect_error());
    }
?> 