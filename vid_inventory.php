<?php
ini_set('display_erros', 'On');
?>


<!DOCTYPE html>
<html>
<header>
  <meta charset="UTF-8">
  <title>Video Inventory</title>
</header>
<body>
  <form action="vid_inventory.php" method="POST">
    <h3>Add a Video:</h3>
    Name: <input type="text" name="name">
    Category: <input type="text" name="category">
    Length: <input type="number" name="length">
    <input type="submit" name="addVideo" value="Add">
  </form>
  <?php
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "lindenba-db", "ntVB3yI2RNUm7xHg", "lindenba-db");
  if($mysqli->connect_errno)
  {
    echo "Failed to connect to MySQL: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }

  $videoTable = 'CREATE TABLE IF NOT EXISTS videoInventory
   (id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(255) NOT NULL,
    length INT, rented INT NOT NULL DEFAULT 0 )';

    if($mysqli->query($videoTable))
    {
      echo "error creating table ";
    }

if($_POST){
  if(isset($_POST["addVideo"])){
      $required=false;

      if(!empty($_POST["name"]))
        $name=$_POST["name"];
      else
      $required=true;

      if(!empty($_POST["category"]))
        $category=$_POST["category"];
      else
        $required=true;

      if(!empty($_POST["category"]))
        $length=$_POST["length"];
      else
          $required=true;
      $rented = 0;

      if($required)
      {
        echo "Could not add video, please enter into all catagories";
      }
      else{

        $newVideo ="INSERT INTO videoInventory (name, category, length, rented)
        VALUES ('$name', '$category', '$length' , '$rented')";

        if($mysqli->query($newVideo))
        {
          echo "Video added";
        }
        else
        {
          echo "Video not added". $newVideo. "<br>".$mysqli->error;
        }
      }

    }
  }
?>


</body>
</html>
