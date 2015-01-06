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
	  
	  $statement="SELECT 
	  sj.PERIOD_ID,sj.COMPANY_ID,sj.PRICE,p.COMPANY_NAME,l.PERIOD_LABEL
	  
	  FROM MAFMARKET_COMP_PERIOD_PRICE sj

	  JOIN MAFMARKET_COMPANY p ON p.COMPANY_ID=sj.COMPANY_ID
	  JOIN MAFMARKET_PERIOD l ON l.PERIOD_ID=sj.PERIOD_ID

	  WHERE sj.PERIOD_ID<='$period_id'

	  ORDER BY PERIOD_ID ASC, COMPANY_ID ASC";


	//$statement="SELECT * FROM MAFMARKET_COMP_PERIOD_PRICE WHERE GAME_ID=1 AND PERIOD_ID=1";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'PERIOD_ID'=>trim(ociresult($s1,'PERIOD_ID'))
						,	'COMPANY_ID'=>trim(ociresult($s1,'COMPANY_ID'))
						,	'PRICE'=>trim(ociresult($s1,'PRICE'))
						,   'COMPANY_NAME'=>trim(ociresult($s1,'COMPANY_NAME'))
						,   'PERIOD_LABEL'=>trim(ociresult($s1,'PERIOD_LABEL'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['PERIOD_ID'].'*'.$value['COMPANY_ID'].'*'.$value['PRICE'].'*'.$value['COMPANY_NAME'].'*'.$value['PERIOD_LABEL'].'~';
			endforeach; 
		endif;
  
  echo $message;
?>