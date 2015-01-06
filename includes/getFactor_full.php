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
	  
	  $period_id = trim($_POST['PostPERIOD_ID']);
	  
	  
	  $statement="SELECT * FROM MAFMARKET_FACTORS WHERE PERIOD_ID <= '$period_id' AND FACTOR_TYPE='news' ORDER BY PERIOD_ID ASC";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'FACTOR_ID'=>trim(ociresult($s1,'FACTOR_ID'))
						,	'FACTOR_TYPE'=>trim(ociresult($s1,'FACTOR_TYPE'))
						,	'FACTOR_STRING'=>trim(ociresult($s1,'FACTOR_STRING'))
						,	'COMPANY_ID'=>trim(ociresult($s1,'COMPANY_ID'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['FACTOR_ID'].'*'.$value['FACTOR_TYPE'].'*'.$value['FACTOR_STRING'].'*'.$value['COMPANY_ID'].'~';
			endforeach; 
		endif;
  
  echo $message;
  //echo 'testing '.$_POST['PostPERIOD_ID'];
?>