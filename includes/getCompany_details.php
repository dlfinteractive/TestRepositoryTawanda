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
	  
	  $comp_id_1 = trim($_POST['PostCOMP1_ID']);
	  $comp_id_2 = trim($_POST['PostCOMP2_ID']);
	  $comp_id_3 = trim($_POST['PostCOMP3_ID']);
	  $comp_id_4 = trim($_POST['PostCOMP4_ID']);
	  $comp_id_5 = trim($_POST['PostCOMP5_ID']);
	  
	  //$statement="SELECT * FROM MAFMARKET_COMPANY WHERE COMPANY_ID IN ('$comp_id_1','$comp_id_2','$comp_id_3','$comp_id_4','$comp_id_5')";
	    $statement="SELECT * FROM MAFMARKET_COMPANY WHERE COMPANY_ID = '$comp_id_1'
		UNION ALL
		SELECT * FROM MAFMARKET_COMPANY WHERE COMPANY_ID = '$comp_id_2'
		UNION ALL
		SELECT * FROM MAFMARKET_COMPANY WHERE COMPANY_ID = '$comp_id_3'
		UNION ALL
		SELECT * FROM MAFMARKET_COMPANY WHERE COMPANY_ID = '$comp_id_4'
		UNION ALL
		SELECT * FROM MAFMARKET_COMPANY WHERE COMPANY_ID = '$comp_id_5'
		";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($resultArray);
				while (ociFetch($s1)) {
				   $resultArray[$count]=array
						(	'COMPANY_NAME'=>trim(ociresult($s1,'COMPANY_NAME'))
						,	'COMPANY_ORIGIN'=>trim(ociresult($s1,'COMPANY_ORIGIN'))
						,	'COMPANY_INDUSTRY'=>trim(ociresult($s1,'COMPANY_INDUSTRY'))
						,	'COMPANY_TYPE'=>trim(ociresult($s1,'COMPANY_TYPE'))
						);
					$count++; 
				}
			//render the html string as a result
			$message='';
			foreach ($resultArray as $value):
				$message.=$value['COMPANY_NAME'].'*'.$value['COMPANY_ORIGIN'].'*'.$value['COMPANY_INDUSTRY'].'*'.$value['COMPANY_TYPE'].'~';
			endforeach; 
		endif;
  
  echo $message;
?>