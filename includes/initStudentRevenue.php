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
	  
	  //insert student details
	 
		$game_inst_id = trim($_POST['PostGAME_INST_ID']);
		$student_id = trim($_POST['PostSTUDENT_ID']);
		$bank_balance = trim($_POST['PostBANK_BALANCE']);
		//$statement="insert into MAFMARKET_STUDENT (STUDENT_FNAME,STUDENT_LNAME,STUDENT_NUMBER) values (:student_fname,:student_lname,:student_number)";
		$statement="merge into MAFMARKET_REVENUE using dual on 
			(STUDENT_ID=:student_id)
		 when matched then 
		     UPDATE SET GAME_INST_ID=:game_inst_id
		 when not matched then 
		    INSERT (GAME_INST_ID,STUDENT_ID,BANK_BALANCE,DIVIDENDS,TRADE_FEES) values (:game_inst_id,:student_id,:bank_balance,0,0)";		
		
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':game_inst_id',$game_inst_id,-1);
		ociBindByName($s1,':student_id',$student_id,-1);
		ociBindByName($s1,':bank_balance',$bank_balance,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: student already exists with number '" . $_POST['PostSTUDENT_ID'] . "'.";
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