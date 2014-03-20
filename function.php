<?php



function db_connect()
//connects to sots and selects database
 {

 ob_start();
		
  $connection = mysql_pconnect('fdb6.awardspace.net', '1597520_09034276', 'Ticket12');
		
	if(!$connection)
	{
		return false;	
	}
		
	if(!mysql_select_db('1597520_09034276'))
	{
		  return false;	
	}
	
	return $connection;
		
 }

function update_schedule()
{

	// If a train has a schedule #2 and now (+ 2hours) > schedule #1 then #2 is made #1
	$userID = $_SESSION['userid']; 

	$s_check = "SELECT trainID, entryt, vacatet, entryt2, vacatet2 FROM train WHERE userID = $userID";
	$s_check_res=mysql_query($s_check);
		
		while($s_check_arr = mysql_fetch_assoc($s_check_res)) {

			// set a var to 2 hours ahead of now
			$now = new DateTime();	
			$schedulechange = $now->modify('+2 hours');
			$vacate = new DateTime($s_check_arr['vacatet']);

				if (($schedulechange > $vacate) && ($s_check_arr['vacatet2'] > '0000-00-00 00:00:00')) {

					$entryt = $s_check_arr['entryt2'];
					$vacatet = $s_check_arr['vacatet2'];
					$trainID = $s_check_arr['trainID'];

					echo $trainID;
					echo "has has its schedule 2 set.";

					$update = "UPDATE train SET `entryt`= '$entryt', 
	      			`vacatet`='$vacatet' WHERE trainID = '$trainID' "; 

	      				mysql_query($update);  	   

	      					$remove = "UPDATE train SET `entryt2`= '0000-00-00 00:00:00', 
	      					`vacatet2`='0000-00-00 00:00:00' WHERE trainID = '$trainID' "; 

	      					mysql_query($remove);  	

	      					// pass id to function that resets the sandbox and checkedat fields. 

	      					reset_sandbox_data($trainID);  
			}
		}	
}

function reset_sandbox_data($trainID)
{
	$insert=sprintf("UPDATE sandbox SET checked='%s', sbLevel='%s', sbDefect='%s', sbDefectComment='%s' WHERE trainID = '$trainID' ",
	mysql_real_escape_string($checked),
	mysql_real_escape_string($sbLevel),
	mysql_real_escape_string($sbDefect),
	mysql_real_escape_string($sbDefectComment),
	mysql_real_escape_string($sbID));

	$checkedat = '0000-00-00 00:00:00';
					
		mysql_query($insert) or die('There has been a problem connecting to the DB. Your changes are not saved.');

			$insert=sprintf("UPDATE train SET checkedat='%s' WHERE trainID = '$trainID'",
						mysql_real_escape_string($checkedat));

				mysql_query($insert) or die('There has been a problem connecting to the DB. Your changes are not saved.');

	
}

function send_warning_email($to, $subject, $body)
{
	// sends email to engineers
	require("mailclass/class.phpmailer.php"); // path to the PHPMailer class
 	
		$mail = new PHPMailer();  
		// enable below to get debug messages
		//$mail->SMTPDebug  = 2;
		$mail->IsSMTP();  // telling the class to use SMTP
		$mail->Mailer = "smtp";
		$mail->Host = "ssl://smtp.gmail.com";
		$mail->Port = 465;
		$mail->SMTPAuth = true; // turn on SMTP authentication
		$mail->Username = "sandboxmonitoringsystem@gmail.com"; // SMTP username
		$mail->Password = "ticket12"; // SMTP password 
		$mail->FromName="Sandbox Management Fault Reporter"; 
		$mail->From     = "sandboxmonitoringsystem@gmail.com";
		foreach($to as $value)
		{
			// go through the email address array for engineers
		   $mail->AddAddress($value); 
		}
		$mail->Subject  = $subject;
		$mail->Body     = "Dear Engineer. The fault comment left is: ".$body;
		$mail->WordWrap = 50;  
		 
		if(!$mail->Send()) {
		echo 'Message was not sent.';
		}

}


function getSettings()
{
	// no longer used.
	$userID=$_SESSION['userid'];
	$query="SELECT max, ahead FROM settings WHERE userID= $userID ";
	$result= mysql_query($query);
	while($row = mysql_fetch_assoc($result)) {
	$_SESSION['maxtrain']=$row['max'];
	$_SESSION['ahead']=$row['ahead'];
	}
}

function isLoggedIn()
// Checks SESSION to see if user is logged in
{
    if(isset($_SESSION['valid']) && $_SESSION['valid'])  { }		
	else {
	echo "<div data-role='page' id='one' data-theme='a'>
	<div data-role='header' data-theme='a'>	
    <h2>You are not logged in. Get out.</h2><a href='index.php?do=logout' data-icon='home' data-iconpos='notext' >Main Menu</a></div>
	<div data-role='content' data-theme='b' > 
	<h2> This is a secure system, please exit the app/browser. <h2>
	<a href='index.php?do=logout' >EXIT</a>"; footer();
	}
}

function noTrain()
{
	// displayed when no trains are registered to user.
	echo "<h1>You have no trains to be checked.</h1>
	<a href='main.php' data-icon='home' data-iconpos='notext' >Main Menu</a>
	<a href='index.php?do=logout' data-icon='search' >Logout</a></div>
	<div data-role='content' data-theme='b'>	
	<h2> You have no trains registered.<h2>"; footer();
}

function noTrainToday()
{	// displayed when no trains on the day for the user..
	echo "<h1>There are no trains due to vacate today.</h1>
	<a href='main.php' data-icon='home' data-iconpos='notext' >Main Menu</a>
	<a href='index.php?do=logout' data-icon='search' >Logout</a></div>
	<div data-role='content' data-theme='b'>	
	<h2> No trains to display based on your settings criteria. <h2>"; footer();
}


function logout() 
// called when user must be logged out
{
    $_SESSION = array(); //destroy all of the session variables
    session_destroy();
}


function footer()
{
 	echo"</div><!-- /content -->
	<div data-role='footer' data-id='foo1' data-position='fixed'>
	<div data-role='navbar'><ul>";
	/*if(isset($_SESSION['type'])){ $type1 = $_SESSION['type']; }
	if ($type1 == 'admin')
		{
		echo"<li><a href='admin.php'>ADMIN</a></li>";
	}
		else{
		echo"<li><a href='settings.php'>SETTINGS</a></li>";
		}
	// no longer needed - was used for development.
	*/ 
	echo"<li><a href='train.php'>TRAINS</a></li>
	<li><a href='schedule.php'>SCHEDULE</a></li>
	
	</ul>
	</div><!-- /navbar -->
	</div><!-- /footer -->		
	</body>
	</html>";
}


function RefOpeningTags ()
{
	// references scripts and opens header.
	echo"<meta name='viewport' content='width=device-width, initial-scale=1' data-dom-cache='true'> 
	<link rel='stylesheet' type='text/css' href='style.css'>
	<link rel='stylesheet' href='http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css' />
	<script src='http://code.jquery.com/jquery-1.9.1.min.js'></script>
	<script src='http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js'></script></head> 
<body> 

<div data-role='page' id='one' data-theme='a'>
	<div data-role='header' data-theme='a'>	";	
}

?>