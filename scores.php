<?php session_start();
//check logged in or not!
if(!isset($_SESSION['login_user'])){
  header('Location:login.php?pagename='.basename($_SERVER['PHP_SELF'], ".php"));
}
?>

<?php

echo "<!doctype html>
<html>
<head>
<meta charset='UTF-8'>
<title>My first Trading Scores</title>
<link type='text/css' href='css/styles.css' rel='stylesheet' media='all' />

<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>

<script language='javascript' src='js/application.js'></script>

</head>
<body>";

// include ('includes/connections.php');

// function  db_connect()
// {
// try
// {
// $db_conn = OCILogon(DB_USERNAME, DB_PASSWORD, DB_NAME);
// return $db_conn;
// }
// catch (Exception $e)
// {
// echo '<p><font color="Red">Report  is currently unavailable. Please contact DLF support.</font></p>';
// }
// return false;
// }

include ('includes/connections.php');
      try
      {
        $conn = db_connect();
      }
      catch (Exception $e)
      {
        die ($e->getMessage());
      }


// try
// {
// $conn = db_connect();
// }
// catch (Exception $e)
// {
// die ($e->getMessage());
// }

//$statement = oci_parse($conn, 'SELECT USERNAME, NAME, EMAIL FROM TEST_USERS');
$statement = oci_parse($conn, 'SELECT STUDENT_NUMBER,STUDENT_FNAME,STUDENT_LNAME,PARTICIPATION,BASIC_TRADING,ADVANCED_TRADING,JOURNAL, ( PARTICIPATION + BASIC_TRADING + ADVANCED_TRADING + JOURNAL ) AS TOTAL FROM MAFMARKET_STUDENT_SCORES ORDER BY TOTAL DESC');
oci_execute($statement);

//$nrows = oci_fetch_all($statement, $res);
$nrows = oci_fetch_all($statement, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_NUM);

//print_r($res);
echo "<div id='score-wrapper'><h2>My First Trading game scores</h2><div class='logout'><p><a href='login.php?ch=logout'><button title='Logout' type='button'>Logout</button></a></p></div>\n";

// Pretty-print the results
echo "<table id='score' class='sortable'>\n";

echo "<thead>
<tr> 
<th>Student Number</th>
<th>First name</th>
<th>Last name</th>
<th>Participation</th>
<th>Basic trading</th>
<th>Advanced trading</th>
<th>Journal</th>
<th>Total score</th>
</tr>
</thead>

<tbody>\n";

foreach ($res as $col) {
    echo "<tr>\n";
    foreach ($col as $item) {
        echo "    <td>".($item !== null ? htmlentities($item, ENT_QUOTES) : "")."</td>\n";
    }

    echo "</tr>\n";
}
echo "<tbody>
</table>\n";

echo "</div>
</body>
</html>";




oci_free_statement($statement);
oci_close($conn);
