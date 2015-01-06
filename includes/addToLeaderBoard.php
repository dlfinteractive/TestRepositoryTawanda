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
	  $revenue_total = trim($_POST['PostREV_TOTAL']);
	  $game_inst_id = trim($_POST['PostGAME_INST_ID']);

	  //echo $student_id." ".$revenue_total." ".$game_inst_id;

	$statement="BEGIN
				MERGE INTO MAFMARKET_LEADER_BOARD
				USING (SELECT 1 FROM DUAL)
				ON (STUDENT_ID = '$student_id')
				WHEN MATCHED THEN UPDATE SET SHARE_TALLY_TOTAL = '$revenue_total'
				WHEN NOT MATCHED THEN 
				INSERT(STUDENT_ID, SHARE_TALLY_TOTAL, GAME_INST_ID) 
				VALUES(:student_id,:revenue_total,:game_inst_id);
				END;";
	$s1 = ociparse($conn, $statement);
	ociBindByName($s1,':student_id',$student_id,-1);
	ociBindByName($s1,':revenue_total',$revenue_total,-1); 
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