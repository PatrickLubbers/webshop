<?php

$connection = connect_to_database();

//############# Data layer

function logError($msg) {
	echo "logging to server: " . $msg;
}

function connect_to_database() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "shops";

    $connection = mysqli_connect($host, $username, $password, $database);

    if (!$connection) {
		throw new Exception("Cannot connect to Db" . mysqli_connect_error());
    }
    return $connection;
}

function add_user_database($connection, $user, $hashedPassword){
	
	//Prevent mysql injections
	$user= mysqli_real_escape_string($connection, $user);
	$hashedPassword = mysqli_real_escape_string($connection, $hashedPassword);

	$insertQuery = "INSERT INT username (username, password_hashed) VALUES ('$user', '$hashedPassword')"; //now whenever wanting to register someone it says user already exists
	$result = mysqli_query($connection, $insertQuery);
	
	if (!$result) {
		throw new Exception("Something wrong with query" . $insertQuery); 
		//mysql_error does a check on the query, but it doesnt throw the exception.
	}
	return $result;
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

function add_to_cart($itemId, $userId, $amount) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the item to the cart session
    $_SESSION['cart'][] = array('userId' => $userId, 'itemId' => $itemId, 'amount' => $amount);
}

function get_order_history($connection, $user, $userId) {
		// Query to get items in the user's cart
        $query = "SELECT orders.id, items.item_name, orders.user_id, orders.amount FROM orders
                  JOIN items ON orders.item_id = items.id
                  WHERE orders.user_id = $userId";

        $result = mysqli_query($connection, $query);
		
		while ($row = mysqli_fetch_assoc($result)) {
			$orderHistory[] = $row;
		}
		
		if (empty($orderHistory)) {
			$orderHistory = ""; //if there is no order history, initialize it here
		} else {
			return $orderHistory;
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
			$amount = $cartItem['amount'];
            insert_into_orders_table($connection, $itemId, $user, $userId, $amount);
        }

        //Clearing the session cart after placing the order
        unset($_SESSION['cart']);
        echo "Order placed successfully!";
    } else {
        echo "Your cart is empty. Add items before placing an order.";
    }
}

function insert_into_orders_table($connection, $itemId, $user, $userId, $amount) {
	
	$userId = get_username_id($connection, $user);
		
        // Insert into the "orders" table
        $query = "INSERT INTO orders (user_id, item_id, amount) VALUES ($userId, $itemId, $amount)";
        $result = mysqli_query($connection, $query);

        if (!$result) {
            die("Error inserting into orders table: " . mysqli_error($connection));
        }
		
    $itemDetails = get_specific_item_details($connection, $itemId);
		//success message
    echo "Order made for item: " . $itemDetails['item_name'];
	echo "<br>";
}

?>