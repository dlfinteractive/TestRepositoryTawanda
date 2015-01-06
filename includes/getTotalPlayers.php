<?php
  // start of session
  session_start();
  
   //create database connection
   include ('connections.php');
      try
      {
        $conn = db_connect();
      }
      catch (Exception $e)
      {
        die ($e->getMessage());
      }
	  
	  //BIT OF BINDING MAYBE?????
	  
	  $game_id = trim($_POST['PostGAME_ID']);

 
$statement = oci_parse($conn, "SELECT STUDENT_ID FROM MAFMARKET_LEADER_BOARD WHERE GAME_INST_ID='$game_id'");
oci_execute($statement);
$nrows = oci_fetch_all($statement, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_NUM);
// $message = $res[0][0];
  
  echo $nrows;
?>