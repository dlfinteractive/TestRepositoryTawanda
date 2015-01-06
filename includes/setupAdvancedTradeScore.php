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
		$advanced_trading = trim($_POST['PostADVANCED_TRADING']);

		$statement="merge into MAFMARKET_STUDENT_SCORES using dual on 
			(STUDENT_ID=:student_id)
		 when matched then 
		     UPDATE SET ADVANCED_TRADING = :advanced_trading 
		 when not matched then 
		    INSERT (ADVANCED_TRADING) values (:advanced_trading)";		
		
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':student_id',$student_id,-1); 
		ociBindByName($s1,':advanced_trading',$advanced_trading,-1);

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