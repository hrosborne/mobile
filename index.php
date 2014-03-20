<!DOCTYPE html> <html> <head> 
<title>SMS Mobile - Login</title> 	
<?php	
		include('function.php'); // includes all functions.
		session_name('mwa'); // gives the session a name, stops conflict between mobile app and web app
		session_start();
		$do = empty($_GET['do']) ? 'index' : $_GET['do']; // sets $do. 	
			
			switch($do) {

				case "logout": // when ?do=logout is passed in the URL, 
				logout(); //u ser logged out
				
				break; }
 		RefOpeningTags(); // includes the rest of <head>, opens page + header	
?>

	<h1>SMS - Please Login</h1>
	</div><!-- /header -->

	<div data-role="content" class= "center" data-theme="a" >	
		<h2>Welcome to the Sandbox Monitoring System.</h2>	
		<p>Your depot administrator will have provided you with a username and password.</p>		
		<p>Please enter these details in the boxes below, then press "Login".</p>
		<br>
		<form action="login.php" method="post" data-ajax="false" class="center">
				<fieldset>
					<div data-role="fieldcontain">
	        			<label for="name">Username: </label>
	         			<input type="text" name="username" id="" value="" />
					</div>
						

				<div data-role="fieldcontain">
	        			<label for="password">Password: </label>
	       		 		<input type="password" name="password" id="" value="" />
				</div>
				<button type="submit" data-theme="b"  data-inline='true' 
				name="login" id="login" value="Ok">Submit</button>
			</fieldset>
		</form>
		<br>
	
	</div><!-- /content -->
	
	<div data-role="footer" data-position="fixed" data-theme="a">
		<h6>This site is constructed as coursework for Oxford Brookes University</h6>
	</div><!-- /footer -->
</div><!-- /page one -->
</body>
</html>


