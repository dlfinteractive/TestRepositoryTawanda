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
	
	   $statement="SELECT COUNT(*) FROM MAFMARKET_LEADER_BOARD WHERE GAME_INST_ID='$game_instance_id'";
	
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			// $count=0;
			// unset($resultArray);
			// 	while (ociFetch($s1)) {
			// 	   $resultArray[$count]=array
			// 			(	'SHARE_TALLY_TOTAL'=>trim(ociresult($s1,'SHARE_TALLY_TOTAL'))
			// 			,	'STUDENT_ID'=>trim(ociresult($s1,'STUDENT_ID'))
			// 			);
			// 		$count++; 
			// 	}
			// //render the html string as a result
			$message='';

				//$message.=$value['SHARE_TALLY_TOTAL'].'*'.$value['STUDENT_ID'].'~';

		endif;
  
  echo $message;
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>