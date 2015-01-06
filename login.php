<?php
//simple PHP login script using Session
//start the session * this is important
session_start();

//login script
if(isset($_REQUEST['ch']) && $_REQUEST['ch'] == 'login'){

	//give your login credentials here
	if($_REQUEST['uname'] == 'admin' && $_REQUEST['pass'] == 'admin123')
		$_SESSION['login_user'] = 1;
	else
		$_SESSION['login_msg'] = 1;
}

//get the page name where to redirect
if(isset($_REQUEST['pagename']))
$pagename = $_REQUEST['pagename'];

//logout script
if(isset($_REQUEST['ch']) && $_REQUEST['ch'] == 'logout'){
	unset($_SESSION['login_user']);
	header('Location:login.php');
}
if(isset($_SESSION['login_user'])){
	if(isset($_REQUEST['pagename']))
	header('Location:'.$pagename.'.php');
	else
	header('Location:score.php');
}else{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login to My First Trading</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <div id="wrapper">

        <form name="login_form" id="login" method="post" action="">

            <p>To access the My First Trading scores, please login using the username and password provided.</p>

            <label for="uname" class="hide">Username</label>
            <input type="text" name="uname" id="uname"placeholder="Username">

            <label for="pass"class="hide">Password</label>
            <input type="password" name="pass" id="pass" placeholder="Password">
			
			<input type="submit" value="Login" class="submit">
            <input type="hidden" name="ch" value="login">
            <?php   
            //display the error msg if the login credentials are wrong!
                if(isset($_SESSION['login_msg'])){
                    echo '<p class="error">Incorrect login details</p>';
                    unset($_SESSION['login_msg']);
                }
            ?>
        </form>
    </div>

<?php } ?>
</body>
</html>
