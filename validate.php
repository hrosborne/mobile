<?php
include('function.php'); // includes all functions.
session_name('mwa');
session_start();
db_connect();

$sbLevel = ($_POST['level']); 
$sbDefect = ($_POST['defect']); 
$sbDefectComment = ($_POST['comment']); 
$sbID = $_SESSION['sbChecked'];
$userID = $_SESSION['userid'];

// validate form data, echo error messages if there are any.

if (!isset($sbLevel)) {

	echo "<p><font color='red'>Please enter the sandbox level.</font></p>";
}

if ((isset($sbLevel)) && ($sbLevel == 0)) {

	echo "<p><font color='red'>Are you sure the sandbox is/was empty?</font></p>";
}

if (($sbDefect == 1) && (empty($sbDefectComment))) {

	echo "<p><font color='red'>Please leave a description of the fault.</font></p>";
}

if ((strlen($sbDefectComment) > 0) && ($sbDefect == 0)) {

	echo "<p><font color='red'>You cannot leave a comment if there is no fault.</font></p>";
}

if ((!isset($sbLevel)) || (($sbDefect == 1) && (empty($sbDefectComment))) 

	|| ((isset($sbLevel)) && ($sbLevel == 0)) || ((strlen($sbDefectComment) > 0) && ($sbDefect == 0)))  { }

else {

// VALIDATION PASSED, NOW PERFORM ALL ACTIONS TO ADD SANDBOX DATA

$checked = "Checked";

// add sandbox level to average, and the number of averages taken.

$occ = "SELECT * FROM fault_occ WHERE sbID = $sbID";
		
		$occres=mysql_query($occ);

			if(mysql_num_rows($occres) == 0) { 

				$level_taken = 1;

             	$insert_occ="INSERT INTO `09034276`.`fault_occ` 
					(`sbID`, `avg_level`, `level_taken`) 
					VALUES ('$sbID', '$sbLevel', '$level_taken');";		

						mysql_query($insert_occ) or die ('avg1 insert failed');
             
              } else {

				while ($occ_row = mysql_fetch_assoc($occres)) {
					$cur_avg= $occ_row['avg_level'];
					$new_avg = ($cur_avg + $sbLevel)/2;
					$level_taken= $occ_row['level_taken'] + 1;
				}

					$insert_occ = "UPDATE fault_occ SET `avg_level`='$new_avg', 
					`level_taken` ='$level_taken' WHERE sbID = '$sbID' "; 

						mysql_query($insert_occ) or die ('avg2 insert failed');

			}

// NOW CHECK if fault is reported - and generate a fault record in repair table.
if ($sbDefect == '1') {

	$insert = "INSERT INTO `09034276`.`repair` 
	(`repairID`, `sbID`, `faultc`, `repairc`, `repairedby`, `repairedat`) 
	VALUES ('', '$sbID', '$sbDefectComment', '', '', '');";
	
	mysql_query($insert) or die ('repair insert failed');

	// take occurance of faults  statistical purposes

	$occ = "SELECT * FROM fault_occ WHERE sbID = $sbID";
		
		$occres=mysql_query($occ);

			if(mysql_num_rows($occres) == 0) { 

				$fault_occ = 1;
				
             	$insert_occ="INSERT INTO `09034276`.`fault_occ` 
					(`sbID`, `fault_occ`) 
					VALUES ('$sbID', '$fault_occ');";		

						mysql_query($insert_occ) or die ('fault1 insert failed');
             
              } else {

				while ($occ_row = mysql_fetch_assoc($occres)) {
					$fault_occ= $occ_row['fault_occ'] + 1;		
				}

					$insert_occ = "UPDATE fault_occ SET `fault_occ`= '$fault_occ' 
	      					 WHERE sbID = '$sbID' "; 

						mysql_query($insert_occ) or die ('fault2 insert failed');
			}

	// now send mail

	$get="SELECT trainID FROM sandbox WHERE sbID= $sbID";
	$result=mysql_query($get);
	while ($row = mysql_fetch_assoc($result)) {
		$mailTid= $row['trainID'];		
	}

	$get="SELECT email FROM user WHERE type= 'eng'";
	$result=mysql_query($get);
	while ($row = mysql_fetch_assoc($result)) {
		$to[]= $row['email'];	
	}

	$subject = 'Repair attention needed at Train ID: '.$mailTid.', Sandbox ID: '.$sbID.'';
	$body = $sbDefectComment;
	
	// call send email function, pass array of emails, subject with relevant ID's
	// and lastly the defect comment.

	send_warning_email($to, $subject, $body);

} // end IF DEFECT IS TRUE

$insert=sprintf("UPDATE sandbox SET checked='%s', sbLevel='%s', 
				sbDefect='%s', sbDefectComment='%s', timechecked=NOW() 
				WHERE sbID ='%s'",
	mysql_real_escape_string($checked),
	mysql_real_escape_string($sbLevel),
	mysql_real_escape_string($sbDefect),
	mysql_real_escape_string($sbDefectComment),
	mysql_real_escape_string($sbID));
			
		mysql_query($insert) or die('There has been a problem connecting to the DB. Your changes are not saved.');


// UNSET SBCHECKED TO PREVENT BUG - NUMBER 3 BUG.
if(isset($_SESSION['sbChecked']))  unset($_SESSION['sbChecked']);

// checks if all sandbox are checked and then sets the last checktime as the trains finished check time
$qry="SELECT trainID FROM sandbox WHERE sbID= $sbID";
$result=mysql_query($qry);
	while ($row = mysql_fetch_assoc($result)) {
		$getTID= $row['trainID'];		
	}
$qry="SELECT numberSb FROM train WHERE trainID = $getTID";
$result=mysql_query($qry);
	while ($row = mysql_fetch_assoc($result)) {
		$getnumberSb= $row['numberSb'];		
	}
for ($i=1; $i<=$getnumberSb; $i++)
 {
	$qry="SELECT timechecked, checked FROM sandbox WHERE trainID = $getTID AND sbName = $i";
	$result=mysql_query($qry);
		while ($row = mysql_fetch_assoc($result)) {
			$sbchecktimes[]=$row['timechecked'];
			$checkvalue[]=$row['checked'];	
		}
}
$lastsbcheck=max($sbchecktimes);

// if all have been checked - set the last check time as train check time
// checks if all values in array are the same (checked, != not checked)
if (count(array_unique($checkvalue)) == 1) {

	$insert="UPDATE train SET checkedat= '$lastsbcheck' WHERE trainID = $getTID";
	mysql_query($insert);
}

// success
// send js script back which is then eval'd to redirect back to train list.
echo "window.location.replace('train.php');";

}

?>




