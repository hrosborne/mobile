<!DOCTYPE html> <html> <head> 

<title>Settings</title> 

<?php
			include('function.php'); // includes all functions.
			session_name('mwa');
			session_start(); // resume session
			error_reporting(E_ALL ^ E_NOTICE);
			RefOpeningTags(); // includes the rest of <head>, opens page + header
			isLoggedIn(); // check if user is logged in
			db_connect(); //connect to db



		
		 

		 	
?>

			<h1>Settings Page</h1>

		<a href="main.php" data-icon="home" data-iconpos="notext" >Main Menu</a>
		
		<a href="" data-icon="search" data-iconpos="notext" data-rel="dialog" data-transition="fade">Search</a>
	</div><!-- /header -->

	<div data-role="content" data-theme="b" >	

		<form action='settings.php' class='center' method='post'>
		
		<fieldset>

			<label class="select"><h3>Maximum number of trains to display at once:</h3></label>
				<select name="max"  id="select-choice-0">
   					<option value="2">2 trains</option>
   					<option value="4">4 trains</option>
   					<option value="6">6 trains</option>
   					<option value="10">10 trains</option>
				</select>
		
		

			<label class="select"><h3>Select number of days ahead of train leaving dates to list trains:</h3></label>
				<select name="ahead"  id="select-choice-0">
					<option value="-1">-1 days</option>
   					<option value="-2">-2 days</option>
   					<option value="-5">-5 days</option>
   					<option value="-7">-7 days</option>

   					<option value="1">1 days</option>
   					<option value="2">2 days</option>
   					<option value="5">5 days</option>
   					<option value="7">7 days</option>
				</select>

			</fieldset>
			</br>
			<button type='submit' data-theme='b' name='submit' data-inline='true'  value='submit-value'>Save</button>

		</form>


		<?php


				$userID=$_SESSION['userid'];
		 	
		 		$max = ($_POST['max']); 

		 		$ahead = ($_POST['ahead']);

   				 if(isset($_POST['max']) && $_POST['max'])  { 

   				 	

   				 	$insert="UPDATE settings SET max= $max WHERE userID= $userID ";

					mysql_query($insert) or die ;

					echo "<h3> Max trains set to: ".$max; echo"</h3>";

   				 }	


   				 if(isset($_POST['ahead']) && $_POST['ahead'])  { 

   				 	

   				 	$insert="UPDATE settings SET ahead= $ahead WHERE userID= $userID ";

					mysql_query($insert) or die ;

					echo "<h3> Days ahead set to: ".$ahead; echo"</h3>";

   				 }	?>


   				 <form action='settings.php' method='post'>
	
	        <label for='slider2'>Reset all sandbox data?</label>
			<select name='reset' id='slider2' data-role='slider'>
				<option value='0'>No</option>
				<option value='1'>Yes</option>
			</select>
	</li>
	<br><br>
	<button type='submit' data-theme='b' name='submit' data-inline='true' value='submit-value'>OK</button>
<form>

<?php

$sbLevel = '0';
$sbDefect = "No";
$sbDefectComment = "";
$checked="Not checked";
$sbID = $_SESSION['sbChecked'];

$reset = ($_POST['reset']); 

if ($reset == '1'){

$insert=sprintf("UPDATE sandbox SET checked='%s', sbLevel='%s', sbDefect='%s', sbDefectComment='%s'",
	mysql_real_escape_string($checked),
	mysql_real_escape_string($sbLevel),
	mysql_real_escape_string($sbDefect),
	mysql_real_escape_string($sbDefectComment),
	mysql_real_escape_string($sbID));

		$checkedat = '0000-00-00 00:00:00';
					
					mysql_query($insert) or die('There has been a problem connecting to the DB. Your changes are not saved.');

					$insert=sprintf("UPDATE train SET checkedat='%s'",
						mysql_real_escape_string($checkedat));


							mysql_query($insert) or die('There has been a problem connecting to the DB. Your changes are not saved.');

					echo "<h1> All users sandbox data has been reset. </h1>";
					
					
}
			

		footer();

		?>