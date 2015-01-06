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
	  
	  
	  //$statement="SELECT JOURNAL_STRING,PERIOD_ID FROM MAFMARKET_STUDENT_JOURNAL WHERE STUDENT_ID = '$student_id'";
	
	   $statement="SELECT sj.JOURNAL_STRING,p.PERIOD_LABEL  
	  				FROM MAFMARKET_STUDENT_JOURNAL sj

					JOIN MAFMARKET_PERIOD p 
					ON p.PERIOD_ID=sj.PERIOD_ID
					WHERE sj.STUDENT_ID='$student_id'
					ORDER BY JOURNAL_ID";
	
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'JOURNAL_STRING'=>trim(ociresult($s1,'JOURNAL_STRING'))
						,	'PERIOD_LABEL'=>trim(ociresult($s1,'PERIOD_LABEL'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['JOURNAL_STRING'].'*'.$value['PERIOD_LABEL'].'~';
			endforeach; 
		endif;
  
  echo $message;
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>