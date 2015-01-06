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
	  $game_id = trim($_POST['PostGAME_ID']);
	  
	  
	  //$statement="SELECT JOURNAL_STRING,PERIOD_ID FROM MAFMARKET_STUDENT_JOURNAL WHERE STUDENT_ID = '$student_id'";
	
	   $statement="SELECT sj.SHARE_TALLY,sj.COMPANY_ID,p.COMPANY_NAME
	  				FROM MAFMARKET_STUDENT_TO_COMPANY sj
					JOIN MAFMARKET_COMPANY p 
					ON p.COMPANY_ID=sj.COMPANY_ID
					WHERE sj.STUDENT_ID='$student_id'
					ORDER BY S2C_ID";
	
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'SHARE_TALLY'=>trim(ociresult($s1,'SHARE_TALLY'))
						,	'COMPANY_NAME'=>trim(ociresult($s1,'COMPANY_NAME'))
						,   'COMPANY_ID'=>trim(ociresult($s1,'COMPANY_ID'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['SHARE_TALLY'].'*'.$value['COMPANY_NAME'].'*'.$value['COMPANY_ID'].'~';
			endforeach; 
		endif;
  
  echo $message;
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>