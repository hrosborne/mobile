<!DOCTYPE html> <html> <head> 

<title>Schedule</title> 

<?php
			include('function.php'); // includes all functions.
      session_name('mwa');
			session_start(); // resume session
			error_reporting(E_ALL ^ E_NOTICE);
			RefOpeningTags(); // includes the rest of <head>, opens page + header
			isLoggedIn(); // check if user is logged in
			db_connect(); //connect to db
      $userID = $_SESSION['userid'];
?>

			<h1>Schedule</h1>

		<a href="index.php?do=logout" data-icon="home" data-iconpos="notext" >Main Menu</a>
		
		<a href="" data-icon="search" data-iconpos="notext" data-rel="dialog" data-transition="fade">Search</a>
	</div><!-- /header -->

<div data-role="content" data-theme="b">

<h2> Displaying all Trains registered to you </h2>

<table data-role="table"  class="ui-body-d ui-shadow table-stripe ui-responsive" data-mode="reflow"  >
         <thead>
           <tr class="ui-bar-d">
             <th data-priority="2">Train Name</th>
             <th data-priority="6"># of SB</th>
             <th data-priority="5">Train Type</th>
             <th data-priority="1">Entry</th>
             <th data-priority="3">Vacate</th>
           </tr>
         </thead>
         <tbody>

<?php

   // generates the schedule table.

  $qry = "SELECT * FROM train WHERE userID = $userID ORDER BY vacatet"; 
  $result = mysql_query($qry);
  while($row = mysql_fetch_assoc($result)) {

       echo"<tr>
            <th>".$row['trainName']."</th>
            <td>".$row['numberSb']."</td>
            <td>".$row['trainType'] ."&nbsp<img src='images/".$row['trainType'].".jpg' width='75' height ='50' /> </td>
            <td><b>".$row['entryt']."</b></td>
            <td><b>".$row['vacatet']."</b></td>
            </tr>";  }   

   echo"</tbody>
       </table> ";   

		footer();

		?>