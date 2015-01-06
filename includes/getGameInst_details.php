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
	  
	  //what game, what period, which companies are in this game, what are their prices for this period
	  $game_instance_id = trim($_POST['PostGAME_INST_ID']);
	  
	  $statement="SELECT * FROM MAFMARKET_GAME_INST WHERE GAME_INST_ID = '$game_instance_id'";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'GAME_ID'=>trim(ociresult($s1,'GAME_ID'))
						,	'START_DATE'=>trim(ociresult($s1,'START_DATE'))
						,	'END_DATE'=>trim(ociresult($s1,'END_DATE'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['GAME_ID'].'*'.$value['START_DATE'].'*'.$value['END_DATE'];
				//$message.=$value['GAME_ID'];
			endforeach; 
		endif;
  
  echo $message;
?>