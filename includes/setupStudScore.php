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
	 	$game_id = trim($_POST['PostGAME_ID']);
		$student_fname = trim($_POST['PostSTUDENT_FNAME']);
		$student_lname = trim($_POST['PostSTUDENT_LNAME']);
		$student_number = trim($_POST['PostSTUDENT_NUMBER']);
		//$statement="insert into MAFMARKET_STUDENT (STUDENT_FNAME,STUDENT_LNAME,STUDENT_NUMBER) values (:student_fname,:student_lname,:student_number)";
		$statement="merge into MAFMARKET_STUDENT_SCORES using dual on 
			(STUDENT_NUMBER=:student_number)
		 when matched then 
		     UPDATE SET STUDENT_FNAME = :student_fname, STUDENT_LNAME = :student_lname 
		 when not matched then 
		    INSERT (STUDENT_ID,GAME_ID,STUDENT_FNAME,STUDENT_LNAME,STUDENT_NUMBER) values (:student_id,:game_id,:student_fname,:student_lname,:student_number)";		
		
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':student_id',$student_id,-1); 
		ociBindByName($s1,':game_id',$game_id,-1);
		ociBindByName($s1,':student_fname',$student_fname,-1);
		ociBindByName($s1,':student_lname',$student_lname,-1);
		ociBindByName($s1,':student_number',$student_number,-1); 
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
		    $message = "success";

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