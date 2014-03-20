
<!DOCTYPE html> 
<html><head>
<title>Sandbox Check Form</title> 
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet"  href="../../css/themes/default/jquery.mobile-1.2.0.css" />  
	<link rel="stylesheet" href="../_assets/css/jqm-docs.css"/>
	<script src="//code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="../../docs/_assets/js/jqm-docs.js"></script>
	<script src="../../js/jquery.mobile-1.2.0.js"></script>
</head> 
<body> 

<div data-role="dialog">

	<script>

	function val(url, action) {                                       
	switch(action)
	{
		case 'val':
			
		var sb= document.getElementById('sbLevel').value
  			var defect= document.getElementById('defect').value
  			var comment= document.getElementById('comment').value
  			

			string= 'level=' + escape(sb) 
			+ '&defect=' + escape(defect) + '&comment=' + escape(comment); 
		break;
	}                
	xmlHttp = new XMLHttpRequest();
	xmlHttp.open('POST', url, true);
	xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	                                
	xmlHttp.onreadystatechange = function() {
	                                   
	if (xmlHttp.readyState == 4) {    
	 
	    document.getElementById("response").innerHTML=xmlHttp.responseText
	    // if the validation passes, a redirect javascript is echo'd back.
	  	eval(xmlHttp.responseText)
	    // it needs to be eval'd to to be exectuted.
	    // else the validation messages are displayed.
		}

	}
	xmlHttp.send(string);
	}   

	</script>

	<div data-role="header" data-theme="d">
					
		<?php
		include('function.php'); // includes all functions.
		session_name('mwa');
		session_start();

		$sb = empty($_GET['sb']) ? 'nosbselected' : $_GET['sb']; 
		// gets the sandbox id that is going to be checked. 

		$key=array_search($sb, $_SESSION['sbArray']); 
		// search the array of all of the 
		//possible sandboxes for that user with the sandbox selected
			
		if($key !== false) {  // if the result isn't false then find out the train id, to help the user.

		db_connect();

			// formulate query
			$qry=sprintf("SELECT * FROM sandbox WHERE sbID='%s'",
    		mysql_real_escape_string($_SESSION['sbArray'][$key]));
	
			// execute query
			$result = mysql_query($qry);
 
			while($row = mysql_fetch_assoc($result))
			{  

				echo"<h1>Sandbox #".$_SESSION['sbArray'][$key];echo ", Train #".$row['trainID']; echo"</h1></div>";

					$checked= $row['checked'];	
					$timechecked= $row['timechecked'];
					$level= $row['sbLevel'];	
					$defect= $row['sbDefect'];	
					$comment= $row['sbDefectComment'];	
					$_SESSION['sbChecked']=$_SESSION['sbArray'][$key];	
								
				echo"<div data-role='content' data-theme='c'>
				<p id='response'> </p>";		
				echo"<form id = 'sbform' action='train.php' method='post'>";
				
				//echo"<form id = 'sbform'>";
				echo"<fieldset>

					<ul data-role='listview' data-inset='true'>
						<li data-role='fieldcontain'>
	        				<label for='slider'>Sand Level (prior to filling):</label>";
	        				//echo"<input type='number' name='sbLevel' id='sbLevel'"; 
	        				
	        				echo"<input type='range' name='sbLevel' id='sbLevel' min='0' max='100'";

	        				if ($checked != 'Not checked') {
	        					// if already checked, set the level that was input.
	        					echo " value='".$level."'/>";
	        				} else {
	        					// else have a default value of 0
	        					echo" value='0' />";
	        				}				
	        				
							echo "</li>
							<li data-role='fieldcontain'>
	        				<label for='slider2'>Equipment Fault?:</label><br>
								<select name='defect' id='defect' data-role='slider'>";			
								
									if ($checked != 'Not checked') {

										if ($defect == 1) {

											echo"<option value='0'>No</option>
									<option selected='selected' value='1'>Yes</option>";

										} else {

											echo"<option selected='selected' value='0'>No</option>
									<option value='1'>Yes</option>";
										}

							  		} else {

							  			echo"<option value='0'>No</option>
									<option value='1'>Yes</option>";
							  		}
									
								echo"</select>
							</li>
							<li data-role='fieldcontain'>
	        				<label for='textarea'>Fault Comment:</label>
								<textarea cols='40' rows='8' name='defectText' id='comment'>";

								if ($checked != 'Not checked') {  echo $comment;
							    } else {
							    // dont put anything
	        					}
								echo"</textarea>	
									</li>";
								if ($checked != 'Not checked') {
							   		echo "<h3>Form filled at ".$timechecked."</h3>";
							    } ?>

								<input value="Submit" data-theme='b' type="button" onclick='val("validate.php","val")'>
								
							<?php  // above calls the ajax function, also setting the destination script.
							echo "<a href='train.php' data-role='button' data-rel='back' data-theme='c'>Go Back</a>   
				
				</fieldset>";
				echo"</form>									 
				</div>
				</div>"; 

				}	
			}
?>
</body>
</html>

