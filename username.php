<?php

session_start();
include 'functions.php';

$connection = connect_to_database();

show_form();
check_login($connection);

function show_form() {
	?>
	
	<html>
<!--login form: -->
<head>
<title>Login form</title>
</head>
<body>

	<form method="post">
		<label for="user">Enter username:</label>
		<input type="text" id="user" name="user"> <!--ID is used for javascript and css styling. name is used for form submission -->
		<label for="password">Enter password:</label>
		<input type="password" id="password" name="password">
		<button type="submit">Submit login</button>
	</form>
</body>
</html>
	
	<?php
}

?>