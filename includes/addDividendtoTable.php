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
	 
		$student_id = trim($_POST['PostSTUDENT_ID']);
		$game_inst_id = trim($_POST['PostGAME_INST_ID']);
		$factor_id = trim($_POST['PostFACTOR_ID']);
		//$statement="insert into MAFMARKET_STUDENT (STUDENT_FNAME,STUDENT_LNAME,STUDENT_NUMBER) values (:student_fname,:student_lname,:student_number)";
		$statement="merge into MAFMARKET_STUDENT_DIVIDENDS using dual on 
			(STUDENT_ID=:student_id)
		 when matched then 
		     UPDATE SET GAME_INST_ID = :game_inst_id
		 when not matched then 
		    INSERT (STUDENT_ID,GAME_INST_ID,FACTOR_ID) values (:student_id,:game_inst_id,:factor_id)";		
		
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':student_id',$student_id,-1);
		ociBindByName($s1,':game_inst_id',$game_inst_id,-1);
		ociBindByName($s1,':factor_id',$factor_id,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: student already exists with number '" . $_POST['PostSTUDENT_NUMBER'] . "'.";
			elseif ($err['code'] == 1400):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in inserting record";
			endif;
		else :
		    $message = $factor_id;

			/*
			//Brett = this is me having a go at trying to work out if it was an update or insert - didn't quite work but it might be useful to know??
			$count=0;
				while (ociFetch($s1)) {
				$count++; 
				}
			$message = $message.$count ;
			*/

		endif;
  
  echo $message;
		
?>