<?php

//#########################################################
//Functions regarding login

function check_login($connection) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['user']) && isset($_POST['password'])) {
			$user = $_POST['user'];
			$password = $_POST['password'];
			
			//showing welcome message
				echo "Welcome " . $user . " !";
				echo '<form method="get" action="shoppingcartA.php">';
				echo '<button type="submit">go to shopping mall</button>';
				
			//store these values as $_SESSION. Even though we dont need $_SESSION password
				store_session_username_password($user, $password, $connection);
		} else {
			show_form();
		}
	}
}

function store_session_username_password($user, $password, $connection){
		$_SESSION['user'] = $user;
		$_SESSION['password'] = $password; //sort of not needed for password
		hash_password($user, $password, $connection);
}

function hash_password($user, $password, $connection){
	$hashedPassword = md5($password);
	update_username_database($user, $hashedPassword, $connection);
}

//add an insert of the $password 
function update_username_database($user, $hashedPassword, $connection){
		$insertQuery = "INSERT INTO username (username, password_hashed) VALUES ('$user', '$hashedPassword')";
		mysqli_query($connection, $insertQuery);
		//make query: add a username
		//make connection to database
}

//###############################################
//Function regarding shopping cart

function show_shopping_page($connection, $result) {
    echo '<form method="post">';
    echo '<table>';
    echo '<tr>
            <th>Item</th>
            <th>Image</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Add to cart</th>
          </tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td><img src="' . $row['image_url'] . '" alt="Item Image" style="width:50px;height:50px;"></td>';
        echo '<td>' . $row['item_name'] . '</td>';
        echo '<td>' . $row['price'] . '</td>';
		echo '<td>';
		echo '<button type="submit" name="addToCart" value="' . $row['id'] . '">Add to Cart</button>'; //changed this code to change name of button
		echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '</form>';
}

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

function get_item_details($connection, $itemId) {
    $query = "SELECT * FROM items WHERE id = $itemId";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}

//I have a suspicion that the two functions above getUsernameId and getItemDetails is duplicate code
//And therefore redundant.

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
            echo '<h4>Items in Your Cart:</h4>';
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

//#######################################
//Database connection established

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

//whats done: several functions have completely been deleted. 
//I often found that bits of data can easily be retrieved from the database 
//or were already stored inside some variable

?>