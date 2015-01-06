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
		$factor_id = trim($_POST['PostFACTOR_ID']);
		//$statement="insert into MAFMARKET_STUDENT (STUDENT_FNAME,STUDENT_LNAME,STUDENT_NUMBER) values (:student_fname,:student_lname,:student_number)";
		$statement="SELECT DECODE(COUNT(*), 0, 'N', 'Y') REC_EXISTS FROM MAFMARKET_STUDENT_DIVIDENDS WHERE STUDENT_ID=:student_id AND FACTOR_ID=:factor_id";		
		
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':student_id',$student_id,-1);
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
		    $count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'REC_EXISTS'=>trim(ociresult($s1,'REC_EXISTS'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['REC_EXISTS'];
			endforeach; 

		endif;
  
  echo $message;
		
?>