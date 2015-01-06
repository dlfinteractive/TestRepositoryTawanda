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
    $company_id = trim($_POST['PostCOMPANY_ID']);
    $tally_total = trim($_POST['PostTALLY_ID']);
    $student_id = trim($_POST['PostSTUDENT_ID']);
    // $game_id = trim($_POST['PostGAME_INST_ID']);
  
  
    //$statement="SELECT JOURNAL_STRING,PERIOD_ID FROM MAFMARKET_STUDENT_JOURNAL WHERE STUDENT_ID = '$student_id'";

$statement="UPDATE MAFMARKET_STUDENT_TO_COMPANY SET SHARE_TALLY=SHARE_TALLY-'$tally_total' WHERE STUDENT_ID='$student_id' AND COMPANY_ID='$company_id'";
	
	$s1 = ociparse($conn, $statement);
	// ociBindByName($s1,':company_id',$company_id,-1);
	// ociBindByName($s1,':tally_total',$tally_total,-1); 
	// ociBindByName($s1,':student_id',$student_id,-1); 
	// ociBindByName($s1,':game_id',$game_id,-1); 
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
  
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>