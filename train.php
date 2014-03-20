<?php
	echo "<!DOCTYPE html> <html> <head> <title>Check Sandboxes</title> ";
	include('function.php'); // includes all functions.
	session_name('mwa');
	// NOT WORKING ON AWARD SPACE session_start(); // resume session
	error_reporting(E_ALL ^ E_NOTICE);
	RefOpeningTags(); // includes the rest of <head>, opens page + header
	db_connect(); //connect to db
	isLoggedIn(); // check if user is logged in
	$userID = $_SESSION['userid']; // set user id, this is buggy and value is sometimes lost.

// updates the scheduling for the trains. 
// If a train has a schedule #2 and now (+ 2hours) > schedule #1 then #2 is made #1
update_schedule();

//Sets sbArray and trainArray. 
//it is calculated later on in the script, but values are lost upon login.

$qry4=sprintf("SELECT trainID FROM train WHERE userID='%s'",
   					mysql_real_escape_string($userID)); 
$result4= mysql_query($qry4);
if(mysql_num_rows($result4)== 0){ noTrain(); 
} else { // if no trains error message is displayed

getSettings(); // get user preferences - NOT really used anymore.

$qry4="SELECT * FROM train
			WHERE DATE(vacatet)=  CURDATE() 
				AND userID= $userID ORDER BY vacatet ASC";				
$result4= mysql_query($qry4);

if(mysql_num_rows($result4)== 0){ noTrainToday(); } else { 
// no trains on this day error displayed. trains still exist for that user however

	while($row4 = mysql_fetch_assoc($result4)) {
		$sbchecktrainID[] =$row4['trainID'];

		// place vacate datetime into array
		$vacate_DT[]=$row4['vacatet'];
		$_SESSION['vacateDT'] = $vacate_DT;

		// create array of train IDs
		$_SESSION['trainIDArray'] = $sbchecktrainID;
		$hooha = implode(',', $_SESSION['trainIDArray']);

$qry5=("SELECT sbID FROM sandbox WHERE trainID IN ({$hooha})");
$result5=mysql_query($qry5);
	while($row5 = mysql_fetch_assoc($result5)) {
		$blahsbarray[]=$row5['sbID'];
		$_SESSION['sbArray'] =$blahsbarray;
	}
}

// NUMBER OF SBS LEFT CALCULATIONS
if(isset($_SESSION['sbArray']) && $_SESSION['sbArray'])  {

//implode array to comma sepearted string for query
			$sblistquery = implode(',', $_SESSION['sbArray']);
			
			$qrySB=("SELECT count(sbID) FROM sandbox WHERE sbId IN ({$sblistquery}) AND checked = 'Not checked'");			

			$sb_checked_qry = mysql_query($qrySB);

				while($sbrow = mysql_fetch_assoc($sb_checked_qry)) 
				{  	
					$notcheckedassoc = $sbrow;
				}

} // end ifsset Sbarray

if (isset($notcheckedassoc)) {
   
foreach ($notcheckedassoc as $key=>$val)
$notchecked=$val;
}

if ($notchecked == 0) {
		echo "<h1 class=green>Checks COMPLETE</h1>";
		}
		
	else {
		echo"<h1 class=red>Checks INCOMPLETE</h1>";

		}

	echo"<a href='main.php' data-icon='home' data-iconpos='notext' >Main Menu</a>
		
		<a href='index.php?do=logout' data-icon='search' >Logout</a>
	</div><!-- /HEADER -->

	<div data-role='content' data-theme='b'>";
		
	if ($notchecked>0) {
	echo"<h4 class=red>Sandboxes UNCHECKED: ".$notchecked;
	}

echo"</h4><h4>";

	// TIME DISPLAY 

	$timedisplay = new DateTime();	
	echo date_format($timedisplay, 'H:i:s\, l jS F Y');

	echo "</h4><h4>Displaying trains that are due to vacate depot today.</h4>
	<h4>Trains due to leave first are at the top. </h4>";
	
// ALL BELOW THIS IS FOR LISTING THE TRAINS AND SANDBOXES

//define search variables
$userID=$_SESSION['userid'];

// EXECTUE MAIN TRAIN DISPLAY QUERY
$result = mysql_query($qry4);

//define arrays
$trainarray = array();
$sbarray = array();

while($row = mysql_fetch_assoc($result)) // GET TRAIN ID'S FOR USER	
// Begin TRAIN PRINTING OUT WHILE (FIRST)
{  
	// train type
	$trainType= $row['trainType'];

	// total number of sandboxes for each train
	$numberSb = $row['numberSb'];

	//set checked
	$checked = "Checked";

	$checkedat =  $row['checkedat'];

	// THIS BELOW WAS THE FIX TO MY BUG - IT TURNS OUT I HADNT SET $trainID until a later loop
	$trainID  = $row['trainID'];

	//gets count of number of checked sandboxes and calls it sbComplete
	$qry3=sprintf("SELECT COUNT(sbID) AS sbComplete FROM sandbox WHERE trainID='%s' AND checked='%s'",
   					mysql_real_escape_string($trainID),
   					mysql_real_escape_string($checked));

		$result3 = mysql_query($qry3);

			while($row3 = mysql_fetch_assoc($result3)) {

			$sbComplete = $row3['sbComplete'];
			
			} // end while row 3
	
			// get datetimes
			$entryRaw=$row['entryt'];
			$vacateRaw=$row['vacatet'];

			// format them
			$entry = new DateTime($entryRaw);
			$vacate = new DateTime($vacateRaw);

			// splits datetime into seperate date and time
			$ed = $entry->format('d-m-Y');
			$et = $entry->format('H:i');
			$vd = $vacate->format('d-m-Y');
			$vt = $vacate->format('H:i');

			// get difference between vacate time and current time
			$now = new DateTime();	
			$timeleft = $vacate->diff($now);

			$dont_display = $now->modify('-30 minutes');
			$checked_at = new DateTime($row['checkedat']);

			

			// IF HAS BEEN CHECKED FOR MORE THAN 30 mins REMOVE.
			if ((($row['checkedat']) > '0000-00-00 00:00:00') && ($dont_display > $checked_at))

			{ } // NOT DISPLAYED 

			else { // carry on with rest of the script

			// if total number of sandboxes is = to number of sandboxes checked
			if ($numberSb==$sbComplete) {

				// Set the TRAIN selector box to be white instead of blue, indicating all checks complete
				echo"<div data-role='collapsible' data-theme='c'  data-icon='check'>";
			
			} else{
				// Still blue - checks remain.
				echo"<div data-role='collapsible'  data-icon='check'>";
			}

			if ($checkedat > 1) {
				echo "<h2>".$row['trainName']." check done at ".$checkedat.".</h2>";

				if ($checkedat > $vacate)
				{
					echo "<h2><font color='red'>Checks were made late.</font></h2>"; // DOESNT WORK

				}

				
			} else {

				// if current time is greater than train vacate time, it is late	
			if ($now > $vacate)
				{
				    echo"<h2><font color='red'>".$row['trainName']." - "; 
				    echo $timeleft->format("Checks late by: %h hours, %i minutes").".</font></h2>"; 
				} else {
				
		echo"<h2>".$row['trainName']." - "; echo $timeleft->format("%h hours, %i minutes");echo" left.</h2>"; 

			}// end else for if train delayed

			}
	
			echo"<h5>Arrived/arriving at: ".$et." on ".$ed; // entry time/date
			echo" and should leave/left at: ".$vt." on ".$vd; // vacate time/date
						
		echo".</h5><ul data-role='listview' data-inset='true' data-split-icon='gear' 
		data-divider-theme='a' data-theme='a' data-split-theme='a'>";
						
			unset($row2['checked']);

			$trainID  = $row['trainID']; // TRAIN ID'S

			// SANDBOX QUERY

			$qry2=sprintf("SELECT DISTINCT sbID, checked, timechecked, sbName FROM sandbox WHERE trainID='%s'",
   					mysql_real_escape_string($trainID));

			$result2=mysql_query($qry2);

 			while($row2 = mysql_fetch_assoc($result2))
    		{    
    			$sbarray[]=$row2['sbID'];

 				// array for the check form
				$_SESSION['sbArray']=$sbarray;

				if ($row2['checked'] != "Checked") {
					// not checked - Black Bar
    				echo "<li>"; 
    			} else{

    				// White bar
					echo"<li data-theme='c'>";
				}

    		echo"<a href='sbcheckform.php?sb=".$row2['sbID']."' data-rel='dialog'>"; 

    		if($row2['checked'] != "Checked") {

    			// if sb not checked
    			
    			//echo"<img src='images/".$trainType.".jpg' />"; OLD IMAGES
    			echo"<img src='images/".$row2['sbName'].".jpg' />";
    			echo"<h3>Sandbox #".$row2['sbID']." </h3>
    			<p><b>".$row2['checked'].".</b></p>";
    		}
    		else{
    			// checked - place tick for image, and time checked.
    			echo"<img src='images/tick.png' /> 
    			<h3>Sandbox #".$row2['sbID']." </h3>
    			<p><b>".$row2['checked']." at: ".$row2['timechecked'].".</b></p>";
    		}
			
			echo"</a><a href='sbcheckform.php?sb=".$row2['sbID']." ' data-rel='dialog'>Open Check Form</a>
			</a></li>";

   			 }// END SECOND while ?>

		</ul>
</div>

<?php

}// END  FIRST WHILE

			} // END NO DISPLAYED (CHECKED AT SET > 0)

}// end NO TRAINs at all for user/

}// end NO TRAINS due to vacate today.

footer(); //print footer.

//testfoot(); // test

?>










