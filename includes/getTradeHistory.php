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
	  $history_string = trim($_POST['PostHISTORY_STRING']);
	  $game_inst_id = trim($_POST['PostGAME_INST_ID']);
	
	   $statement="SELECT * FROM MAFMARKET_TRADE_HISTORY WHERE STUDENT_ID = '$student_id' AND GAME_INST_ID = '$game_inst_id' ORDER BY HISTORY_ID";
	
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'HISTORY_STRING'=>trim(ociresult($s1,'HISTORY_STRING'))
						);
					$count++; 
				}
				$message='';
				if(count($resultArray)>0){
					//render the html string as a result
					foreach ($resultArray as $value):
						$message.=$value['HISTORY_STRING'].'*';
					endforeach; 

				}else{
					$message='noresult';
				}
			
		endif;
  
  echo $message;
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>