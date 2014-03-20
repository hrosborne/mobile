<!DOCTYPE html> <html> <head> <title>SMS Mobile</title> 	
		<?php
			session_name('mwa');
			session_start(); //must call session_start before using any $_SESSION variables
			error_reporting(E_ALL ^ E_NOTICE); // ignores error reports.
			include('function.php'); // includes all functions.
			RefOpeningTags(); // includes the rest of <head>, opens page + header
		?>

	<h1>SMS</h1>
	<a href="index.php?do=logout" data-role="button" data-icon="delete" 
	data-iconpos="left" data-mini="true" data-inline="true">Logout</a>
		
	</div><!-- /header -->

	<div data-role="content" style="margin:0 auto; margin-left:auto; 
	margin-right:auto; align:center; text-align:center;" data-theme="a" >	
	
<?php
	// CONVERT FORM VALUES TO PHP VARIABLE
if(isset($_POST['username'])){ $user = $_POST['username']; } 
if(isset($_POST['password'])){ $pw = $_POST['password']; } 

	db_connect();
	$qry = "SELECT userid FROM user WHERE username='$user' AND password='$pw'"; 
	$result = mysql_query($qry);
	$row = mysql_fetch_array($result);	 
	$_SESSION['userid'] = $row['userid'];

		if(mysql_num_rows($result) < 1) //no such user exists
			{
				unset($_SESSION['userid']);
				// LOGIN FAIL
				echo "<h1>Login Failed</h1>";
				echo"<br>";
				echo "<h2>Incorrect login details.</h2>";
				echo"<a href='index.php' data-role='button' data-theme='b' data-inline='true'>Go back to login</a>";
		echo"</div><!-- /content -->";
		echo"<div data-role='footer' data-position='fixed' data-theme='a'>";
		echo"<h6>This site is constructed as coursework for Oxford Brookes University</h6>";
echo"</div></div></body></html>";

			}
		else
				// LOGIN SUCCESS 
				// the main menu is loaded via ajax - as a result it must all be echod for security reasons 
				// without the echos, if the user failed to log in 
			//they would still be able to view the code for the main menu and gain unauthorized entry
			{	

				$qry2= "SELECT * FROM user WHERE username='$user' AND password='$pw'"; 
				$result2=mysql_query($qry2);
				$row = mysql_fetch_array($result2);	
					//sets the session data for this user
		  			 
		  			 // DOESNT WORK ON AWARD SPACE FOR SOME REASON session_regenerate_id (); 

						$_SESSION['valid'] = 1;
						$_SESSION['type'] = $row['type'];

							$qry= "SELECT login FROM news WHERE newsid = '1'"; 
							$result=mysql_query($qry);
							$row = mysql_fetch_array($result);	

							// login message
							echo "<h2>".$row['login']."</h2>";

							echo"<h2>Your ID is ".$_SESSION['userid'].".</h2>";
							echo"<h2>Your account level is ".$_SESSION['type'].".</h2>";
							echo"<br>";
							echo"<p><a href='train.php'= data-role='button' 
							data-inline='true' data-theme-'b'>Press to continue!</a></p>";
		echo"</div><!-- /content -->";

echo"<div data-role='footer' data-position='fixed' data-theme='a'>";

echo"<h6>This site is constructed as coursework for Oxford Brookes University</h6>";

echo"</div><!-- /footer -->"; // end of footer

echo"</div><!-- /page one --></body></html>"; 
									
		}
?>	


						
			
			
		
	

	