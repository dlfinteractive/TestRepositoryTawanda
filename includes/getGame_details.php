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
	  
	  $statement="SELECT * FROM MAFMARKET_GAME WHERE GAME_ID = '$game_id'";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'INIT_BANK'=>trim(ociresult($s1,'INIT_BANK'))
						,	'COMPANY_ID_1'=>trim(ociresult($s1,'COMPANY_ID_1'))
						,	'COMPANY_ID_2'=>trim(ociresult($s1,'COMPANY_ID_2'))
						,	'COMPANY_ID_3'=>trim(ociresult($s1,'COMPANY_ID_3'))
						,	'COMPANY_ID_4'=>trim(ociresult($s1,'COMPANY_ID_4'))
						,	'COMPANY_ID_5'=>trim(ociresult($s1,'COMPANY_ID_5'))
						,	'GAME_INTRO_LENGTH'=>trim(ociresult($s1,'GAME_INTRO_LENGTH'))
						,	'GAME_PERIOD_LENGTH'=>trim(ociresult($s1,'GAME_PERIOD_LENGTH'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['INIT_BANK'].'*'.$value['COMPANY_ID_1'].'*'.$value['COMPANY_ID_2'].'*'.$value['COMPANY_ID_3'].'*'.$value['COMPANY_ID_4'].'*'.$value['COMPANY_ID_5'].'*'.$value['GAME_INTRO_LENGTH'].'*'.$value['GAME_PERIOD_LENGTH'];
			endforeach; 
		endif;
  
  echo $message;
?>