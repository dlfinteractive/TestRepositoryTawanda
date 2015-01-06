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
	  $game_inst_id = trim($_POST['PostGAME_INST_ID']);
	  
	  
	  $statement="SELECT * FROM MAFMARKET_REVENUE WHERE STUDENT_ID = '$student_id' AND GAME_INST_ID = '$game_inst_id'";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'DIVIDENDS'=>trim(ociresult($s1,'DIVIDENDS'))
						,	'TRADE_FEES'=>trim(ociresult($s1,'TRADE_FEES'))
						,	'BANK_BALANCE'=>trim(ociresult($s1,'BANK_BALANCE'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['DIVIDENDS'].'*'.$value['TRADE_FEES'].'*'.$value['BANK_BALANCE'];
			endforeach; 
		endif;
  
  echo $message;
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>