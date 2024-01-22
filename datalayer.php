<?php

$connection = connect_to_database();

//############# Data layer

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


function add_user_database($connection, $user, $hashedPassword){
	
		//Prevent mysql injections
		$user= mysqli_real_escape_string($connection, $user);
		$hashedPassword = mysqli_real_escape_string($connection, $hashedPassword);

		$insertQuery = "INSERT INTO username (username, password_hashed) VALUES ('$user', '$hashedPassword')";
		mysqli_query($connection, $insertQuery);
		//make query: add a username
		//make connection to database
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

function get_items($connection) {
	$items = array();
	$query = "SELECT * FROM items";
	$result = mysqli_query($connection, $query);
	while ($row = mysqli_fetch_assoc($result)) {
			$items[] = $row; //misses closing bracket
	}
	return $items;
}

function get_specific_item_details($connection, $itemId) {
    $query = "SELECT * FROM items WHERE id = $itemId";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result); //returns one row
}

function add_to_cart($itemId, $userId) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the item to the cart session
    $_SESSION['cart'][] = array('userId' => $userId, 'itemId' => $itemId);
}

function show_cart($connection) {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo '<h3>Shopping Cart:</h3>';
        echo '<table>';
        echo '<tr>
                <th>Item Name</th>
                <th>Item ID</th>
              </tr>';

        foreach ($_SESSION['cart'] as $cartItem) {
			$itemId = $cartItem['itemId'];
			$itemDetails = get_specific_item_details($connection, $itemId); // I think I should improve the structure
			
            echo '<tr>';
            echo '<td>' . $itemDetails['item_name']	. '</td>';
            echo '<td>' . $itemId . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo 'Your cart is empty.';
    }
}

function show_previous_orders($connection, $user, $userId) {

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
        echo "User not found. Guests do not have an order history."; 
    }
}

function place_order($userId, $user, $connection) {
	//Check if the user == "guest"
    if (check_if_guest($user)) {
        return; // Stops rest of function
    }
	
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $cartItem) {
            $itemId = $cartItem['itemId'];
            insert_into_cart($connection, $itemId, $user, $userId);
        }

        //Clearing the session cart after placing the order
        unset($_SESSION['cart']);
        echo "Order placed successfully!";
    } else {
        echo "Your cart is empty. Add items before placing an order.";
    }
}

function insert_into_cart($connection, $itemId, $user, $userId) {
	
	$userId = get_username_id($connection, $user);
	
	//get cart information
	
        // Insert into the "cart" table
        $query = "INSERT INTO cart (user_id, item_id) VALUES ($userId, $itemId)";
        $result = mysqli_query($connection, $query);

        if (!$result) {
            die("Error inserting into cart: " . mysqli_error($connection));
        }
		
    $itemDetails = get_specific_item_details($connection, $itemId);
		//success message
    echo "Item added to the cart: " . $itemDetails['item_name'];
	echo "<br>";
}

?>