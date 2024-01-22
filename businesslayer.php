<?php

//Business layer

$connection = connect_to_database();

//business layer
function handle_login($connection) {
	$loginData = validate_login($connection); //necessary to check whether right data has been passed in login form
		if ($loginData['valid']) {
			do_login_user($loginData['user']);
		} else {
			echo "Login unsuccessful. Error: {$loginData['userErr']}, {$loginData['passwordErr']}";
		}
}

function validate_login($connection) {
	$user = $password = ""; //do you set both variables to "" like this?
	$userErr = $passwordErr = "";
	$valid = false;
	
	//do validation
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (!empty($_POST['user'])) {
				$user = $_POST['user'];
		} else {
				$userErr = "Gebruikersnaam is verplicht";
		} 
		if (!empty($_POST['password'])) {
				$password = $_POST['password'];
		} else {
				$userErr = "Password is verplicht";
		}
		if (empty($userErr) && empty($passwordErr)) {
				$userData = authenticate_user($connection, $user, $password);
				
			if ($userData == "non-existent" || $userData == "password incorrect") { // I dont think this is the best way of doing things
					$userErr = "Gebruiker onbekend of verkeerd password";
			} else {
					$valid = true;
			}
		}
	}
	//returns array with all fields
	return ['user' => $user, 'userErr' => $userErr, 'password' => $password, 'passwordErr' => $passwordErr, 'valid' => $valid];
}

function authenticate_user($connection, $user, $password) {
	// Authenticate username
    $userData = retrieve_userdata($connection, $user);

    if ($userData !== null) {
        // Username exists, now verify the password
        $hashedPassword = $userData['password_hashed'];

        if (password_verify($password, $hashedPassword)) {
            // Password is correct
            return "credentials correct";
        } else {
            // Password is incorrect
            return "password incorrect";
        }
    } else {
        // Username does not exist
        return "non-existent";
    }
}

function handle_registration($connection) {
	$registrationData = validate_registration($connection);
		if ($registrationData['valid']) {
			do_registration_user($connection, $registrationData['user'], $registrationData['password']);
			echo "Registration succesful!";
		} else {
			echo "Registration unsuccesful! Error: {$registrationData['userErr']}, {$registrationData['passwordErr']}";
		}
}

function validate_registration($connection) {
	$user = $password = ""; //do you set both variables to "" like this?
	$userErr = $passwordErr = "";
	$valid = false;
	//add emailErr variable
	
	//do validation
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['register_user'])) {
				$user = $_POST['register_user'];
		} else {
				$userErr = "Gebruikersnaam is verplicht";
		} 
		if (isset($_POST['register_password'])) {
				$password = $_POST['register_password'];
		} else {
				$userErr = "Password is verplicht";
		}
		if (empty($userErr) && empty($passwordErr)) { //both fields filled in?
				$userData = authenticate_user($connection, $user, $password); //check whether exists in database
				if ($userData == "password incorrect") {
					$userErr = "Gebruiker bestaat al!";
				} else {
					$valid = true;
				}
		}
	}
	//returns array with all fields
	return ['user' => $user, 'userErr' => $userErr, 'password' => $password, 'passwordErr' => $passwordErr, 'valid' => $valid];
}

function hash_password($password){
	$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 14]);
	return $hashedPassword;
}

function do_login_user($user) {
	$_SESSION['user'] = $user;
	show_welcome_message($user);
}

function do_registration_user($connection, $user, $password) {
	$hashedPassword = hash_password($password);
	add_user_database($connection, $user, $hashedPassword);
}

function check_if_guest($user) {
    //Check if the user == "guest"
    if ($user == "guest") {
        echo 'Guest users cannot place orders. Please <a href="http://localhost/educom/webshop/page_login.php">login</a>';
        return true; //user is guest
    }

    return false; //user is not guest
}

?>