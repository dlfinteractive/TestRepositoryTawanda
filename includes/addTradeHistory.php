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
	  $student_id = trim($_POST['PostSTUDENT_ID']);
	  $history_string = trim($_POST['PostHISTORY_STRING']);
	  $game_inst_id = trim($_POST['PostGAME_INST_ID']);

	  // echo $student_id." ".$journal_string." ".$period_id." ".$game_inst_id;

	$statement="insert into MAFMARKET_TRADE_HISTORY (STUDENT_ID, HISTORY_STRING, GAME_INST_ID) values (:student_id,:history_string,:game_inst_id)";
	$s1 = ociparse($conn, $statement);
	ociBindByName($s1,':student_id',$student_id,-1);
	ociBindByName($s1,':history_string',$history_string,-1); 
	ociBindByName($s1,':game_inst_id',$game_inst_id,-1); 
	if (ociexecute($s1) === FALSE):
	$err =oci_error($s1);
	if ($err['code'] == 1):
	$message = "Error: Module already exists with name '" . $_POST['module_name'] . "'.";
	elseif ($err['code'] == 1400):
	$message = "Error: Fields marked '*' must be filled";
	else : 
	$message = "Error in inserting record";
	endif;
	else :
	$message = "success";
	endif;
	echo $message;

?>