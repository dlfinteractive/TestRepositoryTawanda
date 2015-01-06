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
	  
	  $game_id = trim($_POST['PostGAME_ID']);
	  $student_id = trim($_POST['PostSTUDENT_ID']);
	  
	  $statement="SELECT * FROM MAFMARKET_LEADER_BOARD WHERE GAME_INST_ID = '$game_id' ORDER BY SHARE_TALLY_TOTAL DESC";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'STUDENT_ID'=>trim(ociresult($s1,'STUDENT_ID'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['STUDENT_ID'].'*';
			endforeach; 
		endif;
  
  echo $message;
?>