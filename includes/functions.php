<?php
/**
  * functions.php
  *
  * Contains a number of interface functions to the ESIM database.
  *
  * @package        eSims
  * @author         Harpreet Jaswal <harpreet@deakin.edu.au>
  * @copyright      Deakin University Nov 2008
  * @version        $Id: functions.php,v 1.0 2008/11/01 
  */

     /**
      * include files
      */
	require('DU.php');
	
	define('SITE_BASE_URL', '/learning-futures/interactive-media/diagnostictool/admin/');
	$SITE_BASE_URL='/learning-futures/interactive-media/diagnostictool/admin/';
	// Oracle string buffer0
	define('USERNAME_BUFFER', 80);
	define('PASSWORD_BUFFER', 20);
	define('RESULT_BUFFER', 10);
	
	/**
	 * get_db_conn
	 * 
	 * set up a db connection
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return $settings array
	 */
	 //DIAG TOOL FUNCS
	/*
	this func has now been moved to connections.php
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
		*/
		
	 /**
	 * check_user_logged_in
	 * 
	 * check if a user has logged in
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return $_SESSION['username'];
	 */
	 function check_user_logged_in()
	{
	    if (empty($_SESSION['logged_in']) && empty($_SESSION['username']))
	    {
		redirect_to_login();
	    }
	    else
	    {
		return $_SESSION['username'];
	    }	    
	}
	
	/**
	 * login()
	 * 
	 * set up session variables to indicate the user has logged in
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @param $username string username of user
	 * @return void
	 */
	function login($username)
	{
	    $_SESSION['logged_in'] = true;
	    $_SESSION['username'] = $username;
	    
	    // redirect to admin page
	    redirect_to_admin();
	}
	
	//START STOCK GAME FUNCS------------------
	
	/* function to get the module details by passing module_id as parameter - for generating the form for modifying the table.
	*/
	function getGame($db_conn,$game_id) {
		// $statement = "select * FROM MAFMARKET_GAME where GAME_ID='$game_id'";
		$statement = "select * FROM MAFMARKET_GAME";

		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			if (($num_rows = ocifetchstatement($s1, $result)) === FALSE):
				echo "Error: no records returned";
			endif;
			
			return($result);
		endif;
	}
	
	//END STOCK GAME FUNCS------------------------------------
	
	//modules funcs
	function get_modules($conn) {
		$statement="SELECT * FROM M303DIAG_MODULES";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($modules);
				while (ociFetch($s1)) {
				   $modules[$count]=array
						(	'MODULE_ID'=>trim(ociresult($s1,'MODULE_ID'))
						,	'NAME'=>trim(ociresult($s1,'NAME'))
						,	'DESCRIPTION'=>trim(ociresult($s1,'DESCRIPTION'))
						);
					$count++; 
				}
			return($modules);
		endif;
	}
	
	
	/**
	* function to insert a record into MODULES table as per posted values.
	*/
	function insert_into_modules($conn, $username) {
		$module_name = strtoupper(trim($_POST['module_name']));
		$module_desc = strtoupper(trim($_POST['module_desc']));
		$statement="insert into M303DIAG_MODULES (NAME,DESCRIPTION) values (:module_name,:module_desc)";
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':module_name',$module_name,-1);
		ociBindByName($s1,':module_desc',$module_desc,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: Module already exists with name '" . $_POST['module_name'] . "'.";
			elseif ($err['code'] == 1400):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in inserting record";
			endif;
		else :
			$message = "Project created successfully" ;
		endif;
		return($message);
	}
	
	/**
	* function to delete a record from MODULES table.
	*/
	function delete_from_modules($db_conn) {
		$message = "init";
			$statement="delete from M303DIAG_MODULES where module_id={$_GET['del_qbt_id']}";
			if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):			
				$err =oci_error($s1);
				if ($err['code'] == 2292):
					$message = "Error: Cannot delete Module '" . $_GET['del_qbt_id'] . "'. Child record exists.";
				else : 
					$message = "Error in deleting record";					
				endif;
			else :
					$message = "Module deleted successfully" ;
			endif;
			return($message);
		}
	
	
	
	/**
	* function to update the Modules table as per the posted values.
	*/
	function update_module($db_conn, $username) {
		$module_name = strtoupper(trim($_POST['upd_module_name']));
		$module_desc = strtoupper(trim($_POST['upd_module_desc']));
		$module_id = trim($_POST['upd_module_id']);
		$statement="update M303DIAG_MODULES 
					set NAME='$module_name'
					, DESCRIPTION='$module_desc'
					where MODULE_ID='$module_id'";
		/*$statement="update M303DIAG_MODULES 
					set NAME='changed'
					, DESCRIPTION='changed'
					 where MODULE_ID={$_POST['upd_module_id']}";*/

		$s1 = ociparse($db_conn, $statement);
		//ociBindByName($s1,':module_name',$module_name,-1);
		//ociBindByName($s1,':module_desc',$module_desc,-1);
		if (ociexecute($s1) === FALSE):
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: Module already exists with name '" . $_POST['upd_module_name'] . "'.";
			elseif ($err['code'] == 1407):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in updating record";
			endif;
		else :
			$message = "Module modified successfully";
		endif;
		return($message);
	}
	
	//delivmodes funcs
	function get_delivmodes($conn) {
		$statement="SELECT * FROM M303DIAG_DELIVMODES";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($delivmodes);
				while (ociFetch($s1)) {
				   $delivmodes[$count]=array
						(	'DELIVMODE_ID'=>trim(ociresult($s1,'DELIVMODE_ID'))
						,	'NAME'=>trim(ociresult($s1,'NAME'))
						,	'DESCRIPTION'=>trim(ociresult($s1,'DESCRIPTION'))
						);
					$count++; 
				}
			return($delivmodes);
		endif;
	}
	
	
	/**
	* function to insert a record into delivmodeS table as per posted values.
	*/
	function insert_into_delivmodes($conn, $username) {
		$delivmode_name = strtoupper(trim($_POST['delivmode_name']));
		$delivmode_desc = strtoupper(trim($_POST['delivmode_desc']));
		$statement="insert into M303DIAG_delivmodeS (NAME,DESCRIPTION) values (:delivmode_name,:delivmode_desc)";
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':delivmode_name',$delivmode_name,-1);
		ociBindByName($s1,':delivmode_desc',$delivmode_desc,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: delivmode already exists with name '" . $_POST['delivmode_name'] . "'.";
			elseif ($err['code'] == 1400):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in inserting record";
			endif;
		else :
			$message = "Delivery mode entry created successfully" ;
		endif;
		return($message);
	}
	
	/**
	* function to delete a record from delivmodeS table.
	*/
	function delete_from_delivmodes($db_conn) {
		$message = "init";
			$statement="delete from M303DIAG_delivmodeS where delivmode_id={$_GET['del_qbt_id']}";
			if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):			
				$err =oci_error($s1);
				if ($err['code'] == 2292):
					$message = "Error: Cannot delete delivmode '" . $_GET['del_qbt_id'] . "'. Child record exists.";
				else : 
					$message = "Error in deleting record";					
				endif;
			else :
					$message = "delivmode deleted successfully" ;
			endif;
			return($message);
		}
		
		/**
	* function to get the delivmode details by passing delivmode_id as parameter - for generating the form for modifying the table.
	*/
	function get_delivmode_detail($db_conn,$delivmode_id) {
		$statement = "select * FROM M303DIAG_DELIVMODES 
						where DELIVMODE_ID='$delivmode_id'";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			if (($num_rows = ocifetchstatement($s1, $result)) === FALSE):
				echo "Error: no records returned";
			endif;
			
			return($result);
		endif;
	}
	
	/**
	* function to update the delivmodes table as per the posted values.
	*/
	function update_delivmode($db_conn, $username) {
		$delivmode_name = strtoupper(trim($_POST['upd_delivmode_name']));
		$delivmode_desc = strtoupper(trim($_POST['upd_delivmode_desc']));
		$delivmode_id = trim($_POST['upd_delivmode_id']);
		$statement="update M303DIAG_delivmodeS 
					set NAME='$delivmode_name'
					, DESCRIPTION='$delivmode_desc'
					where DELIVMODE_ID='$delivmode_id'";
		/*$statement="update M303DIAG_delivmodeS 
					set NAME='changed'
					, DESCRIPTION='changed'
					 where delivmode_ID={$_POST['upd_delivmode_id']}";*/

		$s1 = ociparse($db_conn, $statement);
		//ociBindByName($s1,':delivmode_name',$delivmode_name,-1);
		//ociBindByName($s1,':delivmode_desc',$delivmode_desc,-1);
		if (ociexecute($s1) === FALSE):
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: delivmode already exists with name '" . $_POST['upd_delivmode_name'] . "'.";
			elseif ($err['code'] == 1407):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in updating record";
			endif;
		else :
			$message = "delivmode modified successfully";
		endif;
		return($message);
	}
	
	//testtypes funcs
	function get_testtypes($conn) {
		$statement="SELECT * FROM M303DIAG_TESTTYPES";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($testtypes);
				while (ociFetch($s1)) {
				   $testtypes[$count]=array
						(	'TESTTYPE_ID'=>trim(ociresult($s1,'TESTTYPE_ID'))
						,	'NAME'=>trim(ociresult($s1,'NAME'))
						,	'DESCRIPTION'=>trim(ociresult($s1,'DESCRIPTION'))
						,	'WEIGHT'=>trim(ociresult($s1,'WEIGHT'))
						);
					$count++; 
				}
			return($testtypes);
		endif;
	}
	
	
	/**
	* function to insert a record into TESTTYPES table as per posted values.
	*/
	function insert_into_testtypes($conn, $username) {
		$testtype_name = strtoupper(trim($_POST['testtype_name']));
		$testtype_desc = strtoupper(trim($_POST['testtype_desc']));
		$statement="insert into M303DIAG_TESTTYPES (NAME,DESCRIPTION) values (:testtype_name,:testtype_desc)";
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':testtype_name',$testtype_name,-1);
		ociBindByName($s1,':testtype_desc',$testtype_desc,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: testtype already exists with name '" . $_POST['testtype_name'] . "'.";
			elseif ($err['code'] == 1400):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in inserting record";
			endif;
		else :
			$message = "Delivery mode entry created successfully" ;
		endif;
		return($message);
	}
	
	/**
	* function to delete a record from TESTTYPES table.
	*/
	function delete_from_testtypes($db_conn) {
		$message = "init";
			$statement="delete from M303DIAG_TESTTYPES where testtype_id={$_GET['del_qbt_id']}";
			if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):			
				$err =oci_error($s1);
				if ($err['code'] == 2292):
					$message = "Error: Cannot delete testtype '" . $_GET['del_qbt_id'] . "'. Child record exists.";
				else : 
					$message = "Error in deleting record";					
				endif;
			else :
					$message = "testtype deleted successfully" ;
			endif;
			return($message);
		}
		
		/**
	* function to get the testtype details by passing testtype_id as parameter - for generating the form for modifying the table.
	*/
	function get_testtype_detail($db_conn,$testtype_id) {
		$statement = "select * FROM M303DIAG_TESTTYPES 
						where TESTTYPE_ID='$testtype_id'";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			if (($num_rows = ocifetchstatement($s1, $result)) === FALSE):
				echo "Error: no records returned";
			endif;
			
			return($result);
		endif;
	}
	
	/**
	* function to update the testtypes table as per the posted values.
	*/
	function update_testtype($db_conn, $username) {
		$testtype_name = strtoupper(trim($_POST['upd_testtype_name']));
		$testtype_desc = strtoupper(trim($_POST['upd_testtype_desc']));
		$testtype_id = trim($_POST['upd_testtype_id']);
		$statement="update M303DIAG_TESTTYPES 
					set NAME='$testtype_name'
					, DESCRIPTION='$testtype_desc'
					where TESTTYPE_ID='$testtype_id'";
		/*$statement="update M303DIAG_TESTTYPES 
					set NAME='changed'
					, DESCRIPTION='changed'
					 where testtype_ID={$_POST['upd_testtype_id']}";*/

		$s1 = ociparse($db_conn, $statement);
		//ociBindByName($s1,':testtype_name',$testtype_name,-1);
		//ociBindByName($s1,':testtype_desc',$testtype_desc,-1);
		if (ociexecute($s1) === FALSE):
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: testtype already exists with name '" . $_POST['upd_testtype_name'] . "'.";
			elseif ($err['code'] == 1407):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in updating record";
			endif;
		else :
			$message = "testtype modified successfully";
		endif;
		return($message);
	}
	
	//qbt funcs
	function get_qbt($conn) {
		$statement="SELECT * FROM M303DIAG_QBLOCKTYPES";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($qbt);
				while (ociFetch($s1)) {
				   $qbt[$count]=array
						(	'QBT_ID'=>trim(ociresult($s1,'QBT_ID'))
						,	'NAME'=>trim(ociresult($s1,'NAME'))
						,	'DESCRIPTION'=>trim(ociresult($s1,'DESCRIPTION'))
						);
					$count++; 
				}
			return($qbt);
		endif;
	}
	
	
	/**
	* function to insert a record into QBT table as per posted values.
	*/
	function insert_into_qbt($conn, $username) {
		$qbt_name = strtoupper(trim($_POST['qbt_name']));
		$qbt_desc = strtoupper(trim($_POST['qbt_desc']));
		$statement="insert into M303DIAG_QBLOCKTYPES (NAME,DESCRIPTION) values (:qbt_name,:qbt_desc)";
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':qbt_name',$qbt_name,-1);
		ociBindByName($s1,':qbt_desc',$qbt_desc,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: qbt already exists with name '" . $_POST['qbt_name'] . "'.";
			elseif ($err['code'] == 1400):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in inserting record";
			endif;
		else :
			$message = "Delivery mode entry created successfully" ;
		endif;
		return($message);
	}
	
	/**
	* function to delete a record from QBT table.
	*/
	function delete_from_qbt($db_conn) {
		$message = "init";
			$statement="delete from M303DIAG_QBLOCKTYPES where qbt_id={$_GET['del_qbt_id']}";
			if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):			
				$err =oci_error($s1);
				if ($err['code'] == 2292):
					$message = "Error: Cannot delete qbt '" . $_GET['del_qbt_id'] . "'. Child record exists.";
				else : 
					$message = "Error in deleting record";					
				endif;
			else :
					$message = "qbt deleted successfully" ;
			endif;
			return($message);
		}
		
		/**
	* function to get the qbt details by passing qbt_id as parameter - for generating the form for modifying the table.
	*/
	function get_qbt_detail($db_conn,$qbt_id) {
		$statement = "select * FROM M303DIAG_QBLOCKTYPES 
						where QBT_ID='$qbt_id'";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			if (($num_rows = ocifetchstatement($s1, $result)) === FALSE):
				echo "Error: no records returned";
			endif;
			
			return($result);
		endif;
	}
	
	/**
	* function to update the qbt table as per the posted values.
	*/
	function update_qbt($db_conn, $username) {
		$qbt_name = strtoupper(trim($_POST['upd_qbt_name']));
		$qbt_desc = strtoupper(trim($_POST['upd_qbt_desc']));
		$qbt_id = trim($_POST['upd_qbt_id']);
		$statement="update M303DIAG_QBLOCKTYPES   
					set NAME='$qbt_name'
					, DESCRIPTION='$qbt_desc'
					where QBT_ID='$qbt_id'";
		/*$statement="update M303DIAG_QBLOCKTYPES 
					set NAME='changed'
					, DESCRIPTION='changed'
					 where qbt_ID={$_POST['upd_qbt_id']}";*/

		$s1 = ociparse($db_conn, $statement);
		//ociBindByName($s1,':qbt_name',$qbt_name,-1);
		//ociBindByName($s1,':qbt_desc',$qbt_desc,-1);
		if (ociexecute($s1) === FALSE):
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: qbt already exists with name '" . $_POST['upd_qbt_name'] . "'.";
			elseif ($err['code'] == 1407):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in updating record";
			endif;
		else :
			$message = "qbt modified successfully";
		endif;
		return($message);
	}
	
	//questions funcs
	function get_questions($conn) {
		//$statement="SELECT * FROM M303DIAG_QUESTIONS";
		$statement="SELECT q.QUESTION_ID, q.QBLOCKTYPE_ID, qbt.NAME, q.QUESTION_STR FROM M303DIAG_QUESTIONS q JOIN M303DIAG_QBLOCKTYPES qbt ON q.QBLOCKTYPE_ID=qbt.QBT_ID";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($questions);
				while (ociFetch($s1)) {
				   $questions[$count]=array
						(	'QUESTION_ID'=>trim(ociresult($s1,'QUESTION_ID'))
						,	'QBLOCKTYPE_ID'=>trim(ociresult($s1,'QBLOCKTYPE_ID'))
						,	'QBLOCKTYPE_NAME'=>trim(ociresult($s1,'NAME'))
						,	'QUESTION_STR'=>trim(ociresult($s1,'QUESTION_STR'))
						);
					$count++; 
				}
			return($questions);
		endif;
	}
	
	
	/**
	* function to insert a record into questions table as per posted values.
	*/
	function insert_into_questions($conn, $username) {
		$qblocktype_id = strtoupper(trim($_POST['qblocktype_id']));
		$question_str = trim($_POST['question_str']);
		$statement="insert into M303DIAG_QUESTIONS (QBLOCKTYPE_ID,QUESTION_STR) values (:qblocktype_id,:question_str)";
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':qblocktype_id',$qblocktype_id,-1);
		ociBindByName($s1,':question_str',$question_str,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: questions already exists with name '" . $_POST['question_str'] . "'.";
			elseif ($err['code'] == 1400):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in inserting record";
			endif;
		else :
			$message = "Delivery mode entry created successfully" ;
		endif;
		return($message);
	}
	
	/**
	* function to delete a record from questions table.
	*/
	function delete_from_questions($db_conn) {
		$message = "init";
			$statement="delete from M303DIAG_QUESTIONS where QUESTION_ID={$_GET['del_questions_id']}";
			if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):			
				$err =oci_error($s1);
				if ($err['code'] == 2292):
					$message = "Error: Cannot delete question '" . $_GET['del_questions_id'] . "'. Child record exists.";
				else : 
					$message = "Error in deleting record";					
				endif;
			else :
					$message = "Question deleted successfully" ;
			endif;
			return($message);
		}
		
		/**
	* function to get the questions details by passing questions_id as parameter - for generating the form for modifying the table.
	*/
	function get_questions_detail($db_conn,$questions_id) {
		$statement = "select * FROM M303DIAG_QUESTIONS 
						where QUESTION_ID='$questions_id'";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			if (($num_rows = ocifetchstatement($s1, $result)) === FALSE):
				echo "Error: no records returned";
			endif;
			
			return($result);
		endif;
	}
	
	/**
	* function to update the questions table as per the posted values.
	*/
	function update_questions($db_conn, $username) {
		$questions_typeid = strtoupper(trim($_POST['upd_questions_typeid']));
		//$questions_str = strtoupper(trim($_POST['upd_questions_str']));
		//maybe leave in original case
		$questions_str = trim($_POST['upd_questions_str']);
		$questions_id = trim($_POST['upd_questions_id']);
		$statement="update M303DIAG_QUESTIONS   
					set QBLOCKTYPE_ID='$questions_typeid'
					, QUESTION_STR='$questions_str'
					where QUESTION_ID='$questions_id'";
	

		$s1 = ociparse($db_conn, $statement);
		//ociBindByName($s1,':questions_name',$questions_name,-1);
		//ociBindByName($s1,':questions_desc',$questions_desc,-1);
		if (ociexecute($s1) === FALSE):
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: questions already exists with name '" . $_POST['upd_questions_name'] . "'.";
			elseif ($err['code'] == 1407):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in updating record";
			endif;
		else :
			$message = "questions modified successfully";
		endif;
		//$message = '$questions_typeid '.$questions_typeid.' $questions_str '.$questions_str.'  $questions_id '.$questions_id;
		return($message);
	}
	
	//question block funcs
	function get_qbs($conn) {
		$statement="SELECT qbs.QBLOCK_ID, qbs.QBLOCKTYPE_ID, qbt.NAME, qbs.TITLE, qbs.DESCRIPTION FROM M303DIAG_QBLOCKS qbs JOIN M303DIAG_QBLOCKTYPES qbt ON qbs.QBLOCKTYPE_ID=qbt.QBT_ID";
		if ((($s1 = ociparse($conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($qbData);
				while (ociFetch($s1)) {
				   $qbData[$count]=array
						(	'QBLOCK_ID'=>trim(ociresult($s1,'QBLOCK_ID'))
						,	'QBLOCKTYPE_ID'=>trim(ociresult($s1,'QBLOCKTYPE_ID'))
						,	'QBLOCKTYPE_NAME'=>trim(ociresult($s1,'NAME'))
						,	'TITLE'=>trim(ociresult($s1,'TITLE'))
						,	'DESCRIPTION'=>trim(ociresult($s1,'DESCRIPTION'))
						);
					$count++; 
				}
			return($qbData);
		endif;
	}
	
	
	/**
	* function to insert a record into question block table as per posted values.
	*/
	function insert_into_qbs($conn, $username) {
		$qblocktype_id = strtoupper(trim($_POST['qblocktype_id']));
		$title_str = trim($_POST['upd_title_str']);
		$desc_str = trim($_POST['upd_desc_str']);
		$statement="insert into M303DIAG_QBLOCKS (QBLOCKTYPE_ID,TITLE,DESCRIPTION) values (:qblocktype_id,:title_str,:desc_str)";
		$s1 = ociparse($conn, $statement);
		ociBindByName($s1,':qblocktype_id',$qblocktype_id,-1);
		ociBindByName($s1,':title_str',$title_str,-1); 
		ociBindByName($s1,':desc_str',$desc_str,-1); 
		if (ociexecute($s1) === FALSE):	
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: questions already exists with name '" . $_POST['title_str'] . "'.";
			elseif ($err['code'] == 1400):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in inserting record";
			endif;
		else :
			$message = "Delivery mode entry created successfully" ;
		endif;
		return($message);
	}
	
	/**
	* function to delete a record from question block table.
	*/
	function delete_from_qbs($db_conn) {
		$message = "init";
			$statement="delete from M303DIAG_QBLOCKS where QBLOCK_ID={$_GET['del_qbs_id']}";
			if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):			
				$err =oci_error($s1);
				if ($err['code'] == 2292):
					$message = "Error: Cannot delete question '" . $_GET['del_questions_id'] . "'. Child record exists.";
				else : 
					$message = "Error in deleting record";					
				endif;
			else :
					$message = "Question deleted successfully" ;
			endif;
			return($message);
		}
		
		/**
	* function to get the question block details by passing questions_id as parameter - for generating the form for modifying the table.
	*/
	function get_qbs_detail($db_conn,$modify_qb_id) {
		$statement = "select * FROM M303DIAG_QBLOCKS 
						where QBLOCK_ID='$modify_qb_id'";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			if (($num_rows = ocifetchstatement($s1, $result)) === FALSE):
				echo "Error: no records returned";
			endif;
			
			return($result);
		endif;
	}
	
	/**
	* function to update the question blocks table as per the posted values.
	*/
	function update_qbs($db_conn, $username) {
		$qbs_id = trim($_POST['upd_qbs_id']);
		$qbs_typeid = strtoupper(trim($_POST['upd_qbs_typeid']));
		$title_str = trim($_POST['upd_title_str']);
		$desc_str = trim($_POST['upd_desc_str']);
		$statement="update M303DIAG_QBLOCKS   
					set QBLOCKTYPE_ID='$qbs_typeid'
					, TITLE='$title_str'
					, DESCRIPTION='$desc_str'
					where QBLOCK_ID='$qbs_id'";
	

		$s1 = ociparse($db_conn, $statement);
		//ociBindByName($s1,':questions_name',$questions_name,-1);
		//ociBindByName($s1,':questions_desc',$questions_desc,-1);
		if (ociexecute($s1) === FALSE):
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: questions already exists with name '" . $_POST['upd_questions_name'] . "'.";
			elseif ($err['code'] == 1407):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in updating record";
			endif;
		else :
			$message = "questions modified successfully";
		endif;
		//$message = '$questions_typeid '.$questions_typeid.' $questions_str '.$questions_str.'  $questions_id '.$questions_id;
		return($message);
	}
	
	
	
	
	
	//------------------------------------------
	//function to return question block types for a drop down
	 function qbtnameDropdown($db_conn){
		 $statement="SELECT QBT_ID, NAME FROM M303DIAG_QBLOCKTYPES ORDER BY NAME";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($results);
				while (ociFetch($s1)) {
				   $results[$count]=array
						(	'QBT_ID'=>trim(ociresult($s1,'QBT_ID'))
						,	'NAME'=>trim(ociresult($s1,'NAME'))
						);
					$count++; 
				}
			return($results);
		endif;
	 }
	 
	 //function to return questions for a drop down
	 function qbnameDropdown($db_conn){
		 $statement="SELECT * FROM M303DIAG_QBLOCKS";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($results);
				while (ociFetch($s1)) {
				   $results[$count]=array
						(	'QBLOCK_ID'=>trim(ociresult($s1,'QBLOCK_ID'))
						,	'QBLOCKTYPE_ID'=>trim(ociresult($s1,'QBLOCKTYPE_ID'))
						,	'TITLE'=>trim(ociresult($s1,'TITLE'))
						,	'DESCRIPTION'=>trim(ociresult($s1,'DESCRIPTION'))
						);
					$count++; 
				}
			return($results);
		endif;
	 }
	 
	 
	 
	 
	 //function to return question linked to a selected question block - both ones that are currently linked and ones that could be linked becasue they match the qblock type id for the selected question block - eg all boolean (true/false) questions
	 function retrieve_qb_list($db_conn){
		 $qbl_id = trim($_POST['select_qb']);
		 //$statement="SELECT * FROM M303DIAG_QBLOCKLISTS where QBLOCK_ID='$qbl_id'";
		 //$statement="SELECT qbl.QBLOCKLIST_ID,  qbl.QBLOCK_ID,  qbl.QUESTION_ID,  q.QUESTION_STR FROM M303DIAG_QBLOCKLISTS qbl JOIN M303DIAG_QUESTIONS q ON qbl.QUESTION_ID=q.QUESTION_ID WHERE qbl.QBLOCK_ID='$qbl_id' ";
		 
		 //this statement will return all the questions linked to the selected question block type with a right join so that questions not currently assigned to the selected question block are also returned but able to be filtered becasue of their null values for QBLOCKLIST_ID, QBLOCK_ID and QUESTION_ID
		 $statement="SELECT qbl.QBLOCKLIST_ID,  qbl.QBLOCK_ID,  q.QUESTION_ID,  q.QUESTION_STR 
		 FROM M303DIAG_QBLOCKLISTS qbl 
		 RIGHT JOIN M303DIAG_QUESTIONS q 
		 ON qbl.QUESTION_ID=q.QUESTION_ID 
		 WHERE q.QBLOCKTYPE_ID='$qbl_id'";
		 
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			unset($results);
				while (ociFetch($s1)) {
				   $results[$count]=array
						(	'QBLOCKLIST_ID'=>trim(ociresult($s1,'QBLOCKLIST_ID'))
						,	'QBLOCK_ID'=>trim(ociresult($s1,'QBLOCK_ID'))
						,	'QUESTION_ID'=>trim(ociresult($s1,'QUESTION_ID'))
						,	'QUESTION_STR'=>trim(ociresult($s1,'QUESTION_STR'))
						);
					$count++; 
				}
			return($results);
		endif;
	 }
	 //END DIAG TOOL FUNCS------------------------------------
	 

	/*function get_db_conn()
	{
	    $dbSettings = array();
	    
	    try 
	    {
		$config = new DU_Config_Xml('include/config/package.conf');
	    
	        $dbSettings = array('username' => $config->database->params->username,
				    'password' => $config->database->params->password,
				    'database' => $config->database->params->dbname);
		
		$db_conn = OCILogon($dbSettings['username'], $dbSettings['password'], $dbSettings['database']);
		return $db_conn;
	    } 
	    catch (Exception $e)
	    {
		display_db_err();
	    }
	    
	    return $dbSettings;
	}
	*/
	
	/**
	 * close_db_conn
	 * 
	 * close a db connection
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return $settings array
	 */
	function close_db_conn($db_conn)
	{
	    ocilogoff($db_conn);
	}
	
	/**
	 * display_db_err
	 * 
	 * display a generic database connection issue message
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function display_db_err()
	{
	    echo '<p><font color="Red">eSims database is currently unavailable. Please contact support.</font></p>';
	    echo '<p><font color="Red">We apologise for any inconvenience caused.</font></p>';
	    DoBottom();
	    exit;	
	}
	
	/**
	 * display_login_err
	 * 
	 * display a generic login error message
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function display_login_err()
	{
	    echo '<p><font color="red">Invalid login details provided.</font></p>';
	}
	
	/**
	 * display_password_change_err
	 * 
	 * display a error message for a failed attempt at changing password
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function display_password_change_err()
	{
	    echo '<p><font color="red">Invalid login details provided.</font></p>';
	}
	
	/**
	 * display_password_mismatch
	 * 
	 * display a error message for new passwords that do not match
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function display_password_mismatch()
	{
	    echo '<p><font color="red">The new passwords provided do not match.</font></p>';
	}
	
	/**
	 * deny_access
	 * 
	 * display a denied access message
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function deny_access()
	{
	    echo '<p><font color="red">You do not have relevant permission to view this page.</font></p>';
	    //DoBottom();
	    exit;
	}
	
	/** 
	 * function to capture database error and displaying it to the user.
	 */
	function display_err($s1)
	{
	    $err =oci_error($s1);
	    echo "<p><font color=\"Red\">Oracle Error: ".$err['code'].". There was a problem executing a database query.  Please contact support.</font></p>\n";
	    //DoBottom();
	    exit;
	}
	
	
	
	

	
	
	/**
	 * logout
	 * 
	 * log out from current session
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function logout()
	{
	    unset($_SESSION['logged_in']);
	    unset($_SESSION['username']);
	    
	    redirect_to_login();
	}
	
	/**
	 * redirect_to_login
	 * 
	 * redirect user to login page
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @param $getParams get parameters to show on login page
	 * @return void
	 */
	function redirect_to_login($getParams=array())
	{
	    $getString = '';
	    $counter = 0;
	    foreach($getParams as $indivParamKey => $indivParamValue)
	    {
		if ($counter == 0)
		{
		    $getString = '?'.$indivParamKey.'='.$indivParamValue;
		}
		else
		{
		    $getString .= '&'.$indivParamKey.'='.$indivParamValue;
		}
		$counter++;
	    }
	    
	    header( 'Location: http://' . $_SERVER['SERVER_NAME'] . SITE_BASE_URL . 'index.php' . $getString);
	}
	
	/**
	 * redirect_to_admin
	 * 
	 * redirect user to admin page
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function redirect_to_admin()
	{
	    header( 'Location: http://' . $_SERVER['SERVER_NAME'] . SITE_BASE_URL . 'admin.php');   
	}
	
	/**
	 * redirect_to_password_change
	 * 
	 * redirect user to admin page to change password
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @return void
	 */
	function redirect_to_password_change()
	{
	    header( 'Location: http://' . $_SERVER['SERVER_NAME'] . SITE_BASE_URL . 'change_password.php');   
	}
	
	/**
	 * authenticate_user
	 * 
	 * authenticate a user against the administrator table
	 * 
	 * @author Jeremy Power <jeremy.power@deakin.edu.au>
	 * @param $username string username
	 * @param $password string password
	 * @return $validateResult string (valid, change or invalid)
	 */
	function authenticate_user($username, $password)
	{ 
		//temp auto validate version until this is sorted out
		//validate is username is un - still temp measure
		if ($username=='un')
		{
			$validateResult = 'VALID';
		}
		else
		{
			$validateResult = 'INVALID';
		}
			//$validateResult = 'VALID';
		return $validateResult;
	}
	function authenticate_userORIG($username, $password)
	{    
	    $validPassword = false;
	    $db_conn = get_db_conn();
	    	    
	    try 
	    {
		// check if the user is in the administrator table
		$stmt = ociparse($db_conn, 'SELECT 
						a.userid, 
						a.origin
					    FROM administrator a
					    WHERE a.userid= :username');
		
		oci_bind_by_name($stmt, ':username', $username);

		if (ociexecute($stmt) === FALSE) // administrator account doesn't exist
		{
		    throw new Exception('Error: Invalid login details provided. Please try again.');
		}
		else // administrator account exists
		{
		    $result = array();
		    while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS))) 
		    {
			$result[] = $row;
		    } 
		    
		    // only one administrator account can exist for a given user
		    // validate the password
		    if (!empty($result))
		    {
			// check if admin is a deakin staff member
			if ($result[0]['ORIGIN'] == 'DEAKIN')
			{
			    $duUser = new DU_User($username);
			    $validPassword = $duUser->validatePassword($password);
			    if ($validPassword)
			    {
				$validateResult = 'VALID';
			    }
			    else
			    {
				$validateResult = 'INVALID';
			    }
			} 
			else // external admin. check password in administrator table
			{
			    $validateResult = validate_ext_admin_password($db_conn, $username, $password);
			}	    
		    }
		}
		
		return $validateResult;
	    } 
	    catch (Exception $e)
	    {
		return false;
	    }
	}
	
	

	/**
	* function to get the list of teams linked to a particular project.
	*/
	function team_dropdown($db_conn,$project) {
		$project = str_replace("'","''",$project);
		$statement = "select team_id,team_name from team where project_id='$project' order by team_name";
		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			display_err($s1);
		else:
			$count=0;
			while (ociFetch($s1)) {
				$teams[$count]=array
					('team_id'=>trim(ociresult($s1,'TEAM_ID'))
					,'team_name'=>trim(ociresult($s1,'TEAM_NAME'))
					);
				$count++;
			}
			return($teams);
		endif;
	} 


	/**
	* function to update record from USER_ROLES table as per posted values.
	*/
	function update_user_roles($db_conn,$username) {
		$userid = strtolower(trim($_POST['upd_userid']));
		$role = trim($_POST['upd_role']);
		$status = trim($_POST['upd_status']);

		$statement="update user_roles 
					set role='$role'
					, status='$status'
					, update_who='{$username}'
					, update_on=sysdate 
					where userid='$userid' 
					and role='$role'" ;

		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):
			$err =oci_error($s1);
			if ($err['code'] == 1):
				$message = "Error: '" . $role . "' role already exists for User Id '" . $userid . "'.";
			elseif ($err['code'] == 1407):
				$message = "Error: Fields marked '*' must be filled";
			else : 
				$message = "Error in updating record";
			endif;
		else :
			$message = "Record updated successfully" ;
		endif;
		return($message);
	}

	/**
	* function to insert record into USER_ROLES table as per posted values.
	*/
	function insert_into_user_roles($db_conn,$username) {
		$userid = strtolower(trim($_POST['userid']));
		$role = strtoupper(trim($_POST['role']));
		$status = strtoupper(trim($_POST['status']));

		// Check whether the userid entered in the form is a valid Deakin Id or nor.
		$user  = check_user($db_conn,$userid);
		if (empty($user['USERNAME'])):
			$message = "Error: Not a valid Deakin UserId" ;
		else:
			$statement="insert into user_roles (userid,role,status,update_who) 
						values ('$userid','$role','$status','{$username}')";

			if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):			
				$err =oci_error($s1);
				if ($err['code'] == 1):
					$message = "Error: '" . $role . "' role already exists for User Id '" . $userid . "'.";
				elseif ($err['code'] == 1400):
					$message = "Error: Fields marked '*' must be filled";
				else : 
					$message = "Error in inserting record";
				endif;
			else :
				$message = "Role granted successfully" ;
			endif;
		endif;
		return($message);
	}

	/**
	* function to get the staff details. Retrieves information from Active Directory and UMS.
	*/
	function check_user($db_conn,$userId) 
    {
	    $result = array();
	    
	    try
	    {
		$user = new DU_User($userId);
		$userDetails['USERNAME']	= $userId;
		$userDetails['GIVEN_NAMES']	= strtoupper($user->getGivenNames());
		$userDetails['SURNAME']		= strtoupper($user->getSurname());
		$userDetails['TITLE']		= strtoupper($user->getTitle());
		$userDetails['EMAIL']		= strtolower($user->getEmail());
		$userDetails['LOCATION']	= strtoupper($user->getCampusCode());
		$userDetails['ACCOUNT_TYPE']	= strtoupper($user->getUserType());
		$userDetails['STAFF_ID']	= $user->getStaffId();
		
		$result = $userDetails;
	    } 
	    catch (Exception $e)
	    {
	    }
	    
	    return $result;
	}

	/**
	* function to delete record from USER_ROLES table.
	*/
	function delete_from_user_roles($db_conn) {
		$statement="delete from user_roles 
					where userid='{$_GET['del_userid']}' 
					and role='{$_GET['del_role']}'";

		if ((($s1 = ociparse($db_conn, $statement)) === FALSE) || (ociexecute($s1) === FALSE)):				
			$err =oci_error($s1);
			if ($err['code'] > 0):
				$message = "Error in deleting record";					
			endif;
		else :
			$message = "Role deleted successfully" ;
		endif;
		return($message);
	}

	


?>
