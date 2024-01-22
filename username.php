<?php

session_start();

include 'functions.php';

show_form();
//check_login($connection);

//presentation layer

function show_form() {
	?>
	
	<html>
<!--login form: -->
<head>
<title>Login form</title>
</head>
<body>

<h4>Login form</h4>
	<form method="post">
		<label for="user">Enter username:</label>
		<input type="text" id="user" name="user"> <!--ID is used for javascript and css styling. name is used for form submission -->
		<label for="password">Enter password:</label>
		<input type="password" id="password" name="password">
		<button type="submit" name="login_submit">Submit login</button>
	</form>
	
		<!--Create registration form-->
		<!--All checks and authentication should be run from the input data -->
<h4>Registration form</h4>
	<form method="post">
		<label for="user">Enter username:</label>
		<input type="text" id="user" name="register_user"> <!--ID is used for javascript and css styling. name is used for form submission -->
		<label for="password">Enter password:</label>
		<input type="password" id="password" name="register_password">
		<button type="submit" name="register_submit">Submit registration</button>
	</form>
		 
</body>
</html>
	
	<?php
}

?>