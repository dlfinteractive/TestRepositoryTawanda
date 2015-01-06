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
		//$statement="insert into MAFMARKET_STUDENT (STUDENT_FNAME,STUDENT_LNAME,STUDENT_NUMBER) values (:student_fname,:student_lname,:student_number)";
		$statement="SELECT DECODE(COUNT(*), 0, 'N', 'Y') REV_EXISTS FROM MAFMARKET_REVENUE WHERE STUDENT_ID=:student_id AND GAME_INST_ID=:game_inst_id";		
		
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':student_id',$student_id,-1);
		ociBindByName($s1,':game_inst_id',$game_inst_id,-1); 
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
		    $count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'REV_EXISTS'=>trim(ociresult($s1,'REV_EXISTS'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['REV_EXISTS'];
			endforeach; 

		endif;
  
  echo $message;
		
?>