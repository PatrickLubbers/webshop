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
				
			if ($userData == null || $userData == "password incorrect") { // I dont think this is the best way of doing things
					$userErr = "Gebruiker onbekend of verkeerd password";
			} else {
					$valid = true;
			}
		}
	}
	//returns array with all fields
	return ['user' => $user, 'userErr' => $userErr, 'password' => $password, 'passwordErr' => $passwordErr, 'valid' => $valid];
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
				$userData = authenticate_user($connection, $user, $password); 
				if ($userData !== null) { 		//dit kan nooit nu null zijn
					$userErr = "Gebruiker bestaat al!";
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
        return null;
    }
}

function hash_password($password){
	$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 14]);
	return $hashedPassword;
}

function do_login_user($user) {
	$_SESSION['user'] = $user;
	show_welcome_message($_SESSION['user']);
}

function do_registration_user($connection, $user, $password) {
	
	try {
	$hashedPassword = hash_password($password);
	add_user_database($connection, $user, $hashedPassword);
	} catch (Exception $er) {
		$genericError = "er is een technische storing. Inloggen is niet mogelijk. Probeer later nog eens";

	}
	
}

function check_if_guest($user) {
    //Check if the user == "guest"
    if ($user == "guest") {
        echo 'Guest users cannot place orders. Please <a href="http://localhost/educom/webshop/page_login.php">login</a>';
        return true; //user is guest
    }

    return false; //user is not guest
}

//TODO: add calculation method. Prices for different products are already stored in the 'items' table.
//When added to cart, the price column should also be read at the right item_id, like amount.
//PHP might be loosely typed, but price column has been stored as a string not an integer, can it 
//Be calculated with? 


//Necessary: $_SESSION['total_price'], after every 'add to cart' click, the the $_SESSION['cart'] should be
//appended with the amount that an item costs, times the number for amount. For that to work, at least
//at some point amount, price and $_SESSION['total_price'] should be integers.

//For that, amount and price should be converted from strings to integers. calculations run, and stored
//in $_SESSION['total_price'], which itself holds an integer, as it will always have to be calculated with.
//Before inserting in 'orders' table, the $_SESSION['total_price'] should be converted from integer to string

//that is assuming inserting an integer $_SESSION['total_price'] wont work.

/* flow:

after $_POST add to cart is clicked: check which item has been clicked on item_id

function: retrieve 'amount' and 'prices': convert variables 'amount' and 'prices' to integers

function 2: calculate total cost: Init $_SESSION['total_cost']
amount * prices == $sum, return $_SESSION['total_cost'] += $sum

Perhaps it is better to make $_SESSION['total_cost'] an array that can be appended with new numbers
and once the place order button is clicked, the sum of $_SESSION['total_cost'] will be inserted 
in total_cost column

Pass $_SESSION['total_cost'] as argument...... where

modify insert_into_orders_table so it can insert a column total_cost as well. Pass

*/


?>