<?php

//Presentation layer:

function show_welcome($user) {
	echo "welcome $user!";
	echo "<br><br>";
}

function show_welcome_message($loginData) {
	echo "Welcome " . $loginData . " !";
	echo '<a href="page_shopping.php">';
	echo '<br>';
	echo '<button type="submit">go to shopping mall</button>';
	echo '</a>';
}

function show_logout_button() {
	echo "Click here to log out:";
	echo '<form method="post">';
	echo '<button type="submit" class="submit" name="logOut">log out</button>';
	echo '</form>';
}

function show_products($items) { 
    echo '<form method="post">';
    echo '<table>';
    echo '<tr>
            <th>Item</th>
            <th>Image</th>
            <th>Item Name</th>
            <th>Price</th>
			<th>Amount</th>
            <th>Add to cart</th>
          </tr>';
	
    foreach($items as $item) {
        echo '<tr>';
        echo '<td>' . $item['id'] . '</td>';
        echo '<td><img src="' . $item['image_url'] . '" alt="Item Image" style="width:50px;height:50px;"></td>';
        echo '<td>' . $item['item_name'] . '</td>';
        echo '<td>' . $item['price'] . '</td>';
		echo '<td>';
		echo '<input type="number" name="amount[' . $item['id'] . ']" min="1" value="1" required>';
		echo '</td>';
		echo '<td>';
		
		//I want the input type to pass the itemID instead of the button
		
		echo '<input type="hidden" id="itemId" name="itemId" value="34657" />';
		
		echo '<button type="submit" class="submit" name="addToCart" value="' . $item['id'] . '">Add to Cart</button>'; //changed this code to change name of button
		echo '</td>';
        echo '</tr>';
    }
	
	echo '<tr>';
    echo '<td><button type="submit" class="submit" name="placeOrder">Place Order</button></td>';
    echo '</tr>';

    echo '</table>';
    echo '</form>';
}

function show_cart($connection) {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo '<h3>Shopping Cart:</h3>';
        echo '<table>';
        echo '<tr>
                <th>Item Name</th>
                <th>Item ID</th>
				<th>Amount</th>
              </tr>';

        foreach ($_SESSION['cart'] as $cartItem) {
			$itemId = $cartItem['itemId'];
			$itemDetails = get_specific_item_details($connection, $itemId); // I think I should improve the structure
			
            echo '<tr>';
            echo '<td>' . $itemDetails['item_name']	. '</td>';
            echo '<td>' . $itemId . '</td>';
			echo '<td>' . $cartItem['amount'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo 'Your cart is empty.';
    }
}

function show_previous_orders($connection, $user, $userId) { //Done: split up function and retrieve data from array
	
    // Check if the user is logged in //Should isset condition be placed here or outside function?
    if (isset($userId)) {  
		
		$orderHistory = get_order_history($connection, $user, $userId);

        echo '<h4>Items that you previously ordered:</h4>';
		
		if (!empty($orderHistory)) {
		
			foreach ($orderHistory as $historyRow) {
				// Display items in the cart
				echo '<table>';
				echo '<tr>
						<th>Item Name</th>
						<th>User number</th>
						<th>Amount</th>
					</tr>';

				echo '<tr>';
                echo '<td>' . $historyRow['item_name'] . '</td>';
                echo '<td>' . $historyRow['user_id'] . '</td>';
				echo '<td>' . $historyRow['amount'] . '</td>';
                echo '</tr>';

            echo '</table>';
			} 
			
		} else {
        echo "User not found. Guests do not have an order history."; 
		} 
	} else {
		echo "Nothing to see here.";
	}
}


?>