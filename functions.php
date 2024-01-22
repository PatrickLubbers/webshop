<?php

$connection = connect_to_database();


//Presentation layer:

//files shoppingcartA.php and username.php also for the most part presentation layer

function show_shopping_page($items) { 

    echo '<form method="post">';
    echo '<table>';
    echo '<tr>
            <th>Item</th>
            <th>Image</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Add to cart</th>
          </tr>';

//TODO: dont get directly from database, just pass $items array and loop through it with foreach
	
    foreach($items as $item) {
        echo '<tr>';
        echo '<td>' . $item['id'] . '</td>';
        echo '<td><img src="' . $item['image_url'] . '" alt="Item Image" style="width:50px;height:50px;"></td>';
        echo '<td>' . $item['item_name'] . '</td>';
        echo '<td>' . $item['price'] . '</td>';
		echo '<td>';
		
		//I want the input type to pass the itemID instead of the button
		echo '<input type="hidden" id="itemId" name="itemId" value="34657" />';
		
		echo '<button type="submit" name="addToCart" value="' . $item['id'] . '">Add to Cart</button>'; //changed this code to change name of button
		echo '</td>';
        echo '</tr>';
    }
//changed this for foreach^^ loop over $items array

    echo '</table>';
    echo '</form>';
}

//testing


if (isset($_POST['login_submit'])) {
	handle_login($connection);
}

if (isset($_POST['register_submit'])) {
	handle_registration($connection);
}


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

function show_welcome_message($loginData) {
	echo "Welcome " . $loginData . " !";
	echo '<a href="shoppingcartA.php">';
	echo '<br>';
	echo '<button type="submit">go to shopping mall</button>';
	echo '</a>';
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

function authenticate_user($connection, $user, $password) {
	// Authenticate username
    $userData = retrieve_userdata($connection, $user);

    if ($userData !== null) {
        // Username exists, now verify the password
        $hashedPassword = $userData['password_hashed'];
		
		//Debugging statements
		echo "Entered Password: " . $password . "<br>";
        echo "Hashed Password from Database: " . $hashedPassword . "<br>";

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

//############# Data layer

/*
function store_session_username($user, $password, $connection){
		$_SESSION['user'] = $user;
}
*/

function retrieve_userdata($connection, $user) {

	// Prevent mysql injections
	$user = mysqli_real_escape_string($connection, $user);
	
    //Gets username from the 'username' table instead of ID
    $query = "SELECT password_hashed FROM username WHERE username = '$user'";
    $result = mysqli_query($connection, $query);
	
    if ($result && mysqli_num_rows($result) > 0) {
        //Username exists, return array with password_hashed for verification
		$userData = mysqli_fetch_assoc($result);
        return $userData;
    } else {
        //Username does not exist, nothing to return
        return null;
    }	
}

function do_login_user($user) {
	$_SESSION['user'] = $user;
	show_welcome_message($user);
}

function do_registration_user($connection, $user, $password) {
	$hashedPassword = hash_password($password);
	add_user_database($connection, $user, $hashedPassword);
}

//add an insert of the $password 
function add_user_database($connection, $user, $hashedPassword){
	
		//Prevent mysql injections
		$user= mysqli_real_escape_string($connection, $user);
		$hashedPassword = mysqli_real_escape_string($connection, $hashedPassword);

		$insertQuery = "INSERT INTO username (username, password_hashed) VALUES ('$user', '$hashedPassword')";
		mysqli_query($connection, $insertQuery);
		//make query: add a username
		//make connection to database
}

/*##########TODO##########
Right now cart is a table in the database. Its better to store the cart data in a session
And write it into the database. 

*/

//###############################################
//Function regarding shopping cart

function get_username_id($connection, $user) {
	
	//Gets username ID from the 'username' table
    $query = "SELECT id FROM username WHERE username = '$user'";
    $result = mysqli_query($connection, $query);
	if ($result && ($row = mysqli_fetch_assoc($result))) {
        return $row['id']; //we need the user_id
    } else {
		return null;
	}
}


/*
TODO FIX:
Dit is een slecht ontwerp, omdat dan onduidelijk is wie het result sluit

Beter is om (in een functie) alle data over te gieten in een array en deze terug te geven.

Zie git issues

*/

function get_items($connection) {
	$items = array();
	$query = "SELECT * FROM items";
	$result = mysqli_query($connection, $query);
	while ($row = mysqli_fetch_assoc($result)) {
			$items[] = $row; //misses closing bracket
	}
	return $items;
}

function get_item_details($connection, $itemId) {
    $query = "SELECT * FROM items WHERE id = $itemId";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}

function add_to_cart($itemId) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the item to the cart session
    $_SESSION['cart'][] = $itemId;
}

//#######################################
//Database connection established. Database access layer:


/*TODO FIX: 

Dit is een slecht ontwerp, omdat
A: zo het password van je root account in de code staat 
(een een root account zonder wachtwoord is zoiezo een slechte gewoonte)

B: de root gebruiker veel meer rechten heeft dat nodig is, 
waardoor hackers met dit account alles kunnen in je database.

Beter is het om een nieuwe user aan te maken in phpMyAdmin
en deze alleen 'SELECT', 'INSERT','UPDATE','DELETE' rechten te geven. en deze te gebruiken in je code.

progress: Lijkt niet te lukken om een nieuw account aan te maken

*/

function connect_to_database() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "shops";

    $connection = mysqli_connect($host, $username, $password, $database);

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $connection;
}

/*
After refactoring I could still use this function to show an 'orders' table:
Show_orders_list($connection, $user, $userId)
*/

function show_what_is_in_cart($connection, $user, $userId) {

    // Check if the user is logged in
    if (isset($userId)) {  //NOTE: changed from if ($userId !== null) { To achieve uniformity
	
        // Query to get items in the user's cart
        $query = "SELECT cart.id, items.item_name, cart.user_id FROM cart 
                  JOIN items ON cart.item_id = items.id
                  WHERE cart.user_id = $userId";

        $result = mysqli_query($connection, $query);

        if ($result) {
            // Display items in the cart
            echo '<h4>Items that you previously ordered:</h4>';
            echo '<table>';
            echo '<tr>
                    <th>Item Name</th>
                    <th>User number</th>
                  </tr>';

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['item_name'] . '</td>';
                echo '<td>' . $row['user_id'] . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            die("Error retrieving cart items: " . mysqli_error($connection));
        }
    } else {
        echo "User not found."; 
    }
}

//$_SESSION['cart'] = $_POST['addtocart'];

/*I could still use this to insert $_SESSION['cart'] data into database table 'orders'.

$_SESSION['cart'] should be an array holding itemId and possibly quantity. 
array key - $itemId, array element - quantity.
perhaps itemId should be passed as input type hidden, 

*/

function insert_into_cart($connection, $itemId, $user, $userId) {
	
	$userId = get_username_id($connection, $user);
	
	//Deleted $_SESSION and retrieved item_name directly from database
	
        // Insert into the "cart" table
        $query = "INSERT INTO cart (user_id, item_id) VALUES ($userId, $itemId)";
        $result = mysqli_query($connection, $query);

        if (!$result) {
            die("Error inserting into cart: " . mysqli_error($connection));
        }
		
    $itemDetails = get_item_details($connection, $itemId);
		//success message
    echo "Item added to the cart: " . $itemDetails['item_name'];
}

//whats done: several functions have completely been deleted. 
//I often found that bits of data can easily be retrieved from the database 
//or were already stored inside some variable

?>