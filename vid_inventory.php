<?php
ini_set('display_errors', 'On');
?>


<!DOCTYPE html>
<html>
  <header>
    <meta charset="UTF-8">
    <title>Video Inventory</title>
  </header>
<body>
    <form action="vid_inventory.php" method="POST">
      <h2>Video</h2>
      <fieldset>
        <legend>Add a video:</legend>
        Name: <input type="text" name="name">
        Category: <input type="text" name="category">
        Length: <input type="number" name="length">
        <input type="submit" name="addVideo" value="Add">
      </fieldset>
    </form>
<?php
//used code from PHP.net, Wolford lectures
//connects to my database on the onid server
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "lindenba-db", "ntVB3yI2RNUm7xHg", "lindenba-db");
  if($mysqli->connect_errno)
  {
    echo "Failed to connect to MySQL: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
//create a table if it doesn't already exist
  $videoTable = 'CREATE TABLE IF NOT EXISTS videoInventory
   (id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(255) NOT NULL,
    length INT, rented INT NOT NULL DEFAULT 0 )';

    if($mysqli->query($videoTable))
    {
    //  echo "error creating table ";
    }
//add a video to the table
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
  //delete individual videos from inventory using id
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

//delete all videos
if(isset($_POST['deleteALLVideo']))
{
  //delete all from inventory
  if(!($stmt = $mysqli->prepare("DELETE FROM videoInventory")))
  {
    echo "Prepare failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
  if(!$stmt->execute())
  {
    echo "Execute failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
  $stmt->close();
  echo '<h2>'. "Video Inventory List". '</h2>';
  echo  '<table border="1">';
  echo  '<tr><td>Name</td><td>Category</td><td>Length</td><td>Available</td><td>Status</td><td>Remove</td></tr>';

}
// else
// {
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
// }

//filter category of the videos
if(isset($_POST['filterCategory']))
{
  if($_POST['filter'] == 'allMovies')
  {
    if(!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM videoInventory")))
    {
      echo "Prepare failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
    }
    else
    {
       if(!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM videoInventory WHERE category=(?)")))
      {
        echo "Prepare failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
      }
      if(!$stmt->bind_param("s", $_POST['filter']))
      {
        echo "Bind failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
      }
    }
  }
}

if(!($stmt = $mysqli->prepare("SELECT DISTINCT category FROM videoInventory")))
  {
     echo "Prepare failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
  if(!$stmt->execute())
  {
      echo "Execute failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
  if(!$stmt->bind_result($Fcategory))
  {
     echo "Bind failed: " .$mysqli->connect_errno. " " .$mysqli->connect_error;
  }
    echo '<fieldset><legend>Filter Videos:</legend>';
    echo '<form method="POST" action="vid_inventory.php">';
    echo '<select name='. "filter" .'>';
  while($stmt->fetch())
  {
    echo '<option value='. $Fcategory. '>' .$Fcategory. '</option>';
  }
    echo '<option selected value='. "allMovies" . '>' . "All Movies" . '</option>';
    echo '<input type= "submit" value="submit" name=' . "filterCategory" . '>';
    echo '</select></fieldset></form>';

    echo '<form method="POST" action="vid_inventory.php">';
    echo '<fieldset><legend>Delete Inventory:</legend><input type="submit" name=' . "deleteALLVideo" . ' value= "Delete"</fieldset></form>';

?>

</body>
</html>
