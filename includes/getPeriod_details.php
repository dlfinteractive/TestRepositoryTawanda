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
	  
	  $statement="SELECT * FROM MAFMARKET_PERIOD WHERE GAME_ID = '$game_id'
	  ORDER BY PERIOD_ORDER";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'PERIOD_ID'=>trim(ociresult($s1,'PERIOD_ID'))
						,	'PERIOD_ORDER'=>trim(ociresult($s1,'PERIOD_ORDER'))
						,	'PERIOD_LABEL'=>trim(ociresult($s1,'PERIOD_LABEL'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['PERIOD_ID'].'*'.$value['PERIOD_ORDER'].'*'.$value['PERIOD_LABEL'].'~';
			endforeach; 
		endif;
  
  echo $message;
?>