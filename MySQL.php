<!DOCTYPE html>
<html>
<header>
  <meta charset="UTF-8">
  <title>Video Inventory</title>
</header>
<body>
  <form action="MySQL.php" method="POST">
    Name: <input type="text" name="video_name">
    Category: <input type="text" name="Type_video">
    Length: <input type="number" name="Length_video" min=0>
    <input type="submit" value="Submit">
  </form>
</body>
</html>
<?php
ini_set('display_erros', 'On');

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "lindenba-db", "ntVB3yI2RNUm7xHg", "lindenba-db");
if($mysqli->connect_errno)
{
  echo "Failed to connect to MySQL: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
}
else
{
  echo "Connection worked!";
}
?>
<?php

// mysql> CREATE TABLE videoInventory  (id INT PRIMARY KEY AUTO_INCREMENT,
//   name VARCHAR(255) UNIQUE NOT NULL,
//   category VARCHAR(255),
//   length INT, rented INT NOT NULL DEFAULT 0 );
