<?php

//define database connection details

//dev
// define('DB_USERNAME','DLF_OWNER');
// define('DB_PASSWORD','cde3vfr4');
// define('DB_NAME','WWWD');

//uat
// define('DB_USERNAME','DLF_OWNER');
// define('DB_PASSWORD','cde3vfr4');
// define('DB_NAME','WWWT');

//prod
define('DB_USERNAME','DLF_OWNER');
define('DB_PASSWORD','cde3vfr4');
define('DB_NAME','WWWD');

 function  db_connect()
		{
			try
			{
				$db_conn = OCILogon(DB_USERNAME, DB_PASSWORD, DB_NAME);
				return $db_conn;
			}
			catch (Exception $e)
			{
				echo '<p><font color="Red">Report  is currently unavailable. Please contact DLF support.</font></p>';
			}
			return false;
		}

?>
