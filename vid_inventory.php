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
      <h2>Add a Video:</h2>
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

//if($_POST){
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
        echo "Could not add video, please enter into all categories";
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
    // else if (isset($_POST["deleteVid"]))
    // {
    //   $deleteEntry = $_POST["deleteVid"];
    //   echo $_POST["deleteVid"];
    //   $delete= "DELETE FROM videos WHERE id = '".$deleteEntry."'";
    //   $deleted=$mysqli->query($delete);
    //   if($deleted)
    //   echo "Video deleted";
    //   else
    //     echo "Unable to delte";
    // }
  //}
//get status of the video, all videos when added to inventory are available.
  if(isset($_POST['statusVideo']))
  {
    if(isset($_POST['statusCheck']))
    {
      if(!($stmt=$mysqli->prepare("UPDATE videoInventory SET rented = (?) WHERE id=(?)")))
      {
        echo "Prepare failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
      }

      if($_POST['statusCheck'] == 'in')
      {
        $inout = false;
      }
      else if($_POST['statusCheck'] == 'out')
      {
        $inout = true;
      }
      if(!$stmt->bind_param("is", $inout, $_POST['statusInOut']))
      {
        echo "Binding failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
      }
      if(!$stmt->execute())
      {
        echo "Execute failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
      }
     $stmt->close();
    }
  }
//delete individual videos
if(isset($_POST ['deleteVideo']))
{
  if(!($stmt = $mysqli->prepare("DELETE FROM videoInventory WHERE id=(?)")))
  {
    echo "Prepare failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
  if(!$stmt->bind_param("s", $_POST['delete']))
  {
    echo "Binding failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
  if(!$stmt->execute())
  {
    echo "Execute failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
 $stmt->close();
}



//select from database get name, cat, len, and rented to add to a table
if(!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM videoInventory")))
{
  echo "Prepare failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
}
if(!$stmt->execute())
{
  echo "Execute failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
}

//bind those results into these variables
if(!$stmt->bind_result($id, $name, $category, $length, $rented))
{
  echo "Bind failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
}
echo '<h2>'. "Video Inventory List". '</h2>';
echo  '<table border="1">';
echo     '<tr><td>Name</td><td>Category</td><td>Length</td><td>Available</td><td>Status</td><td>Remove</td></tr>';
 //this will fetch until it can't fetch any more
while($stmt->fetch())
{
  if($rented == 'false')
  {
    $rented = 'Available';
  }
  else
  {
    $rented = 'Out';
  }
  echo "<tr>\n<td>\n" . $name . "\n</td>\n<td>\n" . $category .
       "\n</td>\n<td>\n" . $length . "\n</td>\n<td>\n". $rented . "\n</td>\n";
  //status of the video
  echo '<td><form method="POST" action="vid_inventory.php">';
  echo "Check-" . "in" . '<input type="radio" name="statusCheck" value='."in". '>';
  echo "out" . '<input type="radio" name="statusCheck" value='."out". '>';
  echo '<input type="hidden" name="statusInOut" value='. $id . '>';
  echo '<input type="submit" value="Update" name=' . "statusVideo" . '></form>';
  echo '<td><form method="POST" action="vid_inventory.php">';
  //delete button
  echo '<input type="hidden" name="delete" value='. $id . '>';
  echo  '<input type="submit" name=' . "deleteVideo" . ' value= "delete" ></form></tr>';
}
$stmt->close();



echo '</table>';

?>

</body>
</html>
