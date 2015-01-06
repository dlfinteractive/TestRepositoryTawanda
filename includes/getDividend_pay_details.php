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
	  
	  $factor_company_id = trim($_POST['PostFACTORY_COMPANY_ID']);
	  
	  
	  $statement="SELECT STUDENT_ID,SHARE_TALLY FROM MAFMARKET_STUDENT_TO_COMPANY WHERE COMPANY_ID = '$factor_company_id'";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'STUDENT_ID'=>trim(ociresult($s1,'STUDENT_ID'))
						,	'SHARE_TALLY'=>trim(ociresult($s1,'SHARE_TALLY'))
						// ,	'FACTOR_STRING'=>trim(ociresult($s1,'FACTOR_STRING'))
						// ,	'COMPANY_ID'=>trim(ociresult($s1,'COMPANY_ID'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['STUDENT_ID'].'*'.$value['SHARE_TALLY'].'~';
			endforeach; 
		endif;
  
  echo $message;
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>