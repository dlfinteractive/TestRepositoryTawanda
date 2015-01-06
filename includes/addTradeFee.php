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
	  $trade_fee = trim($_POST['PostTRADE_FEE']);
	  $game_inst_id = trim($_POST['PostGAME_INST_ID']);

	  // echo $student_id." ".$journal_string." ".$period_id." ".$game_inst_id;

	$statement="update MAFMARKET_REVENUE set TRADE_FEES=TRADE_FEES+$trade_fee where STUDENT_ID=$student_id";
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