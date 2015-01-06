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
	  $period_id = trim($_POST['PostPERIOD_ID']);
	  //$company_id = trim($_POST['PostCOMPANY_ID']);
	  
	  $statement="SELECT * FROM MAFMARKET_COMP_PERIOD_PRICE WHERE GAME_ID='$game_id' AND PERIOD_ID='$period_id'";
	//$statement="SELECT * FROM MAFMARKET_COMP_PERIOD_PRICE WHERE GAME_ID=1 AND PERIOD_ID=1";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'COMPANY_ID'=>trim(ociresult($s1,'COMPANY_ID'))
						,	'PRICE'=>trim(ociresult($s1,'PRICE'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['COMPANY_ID'].'*'.$value['PRICE'].'~';
			endforeach; 
		endif;
  
  echo $message;
?>