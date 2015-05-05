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
