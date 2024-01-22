<?php

//Presentation layer:

function show_welcome_message($loginData) {
	echo "Welcome " . $loginData . " !";
	echo '<a href="page_shopping.php">';
	echo '<br>';
	echo '<button type="submit">go to shopping mall</button>';
	echo '</a>';
}

function show_products($items) { 

    echo '<form method="post">';
    echo '<table>';
    echo '<tr>
            <th>Item</th>
            <th>Image</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Add to cart</th>
          </tr>';
	
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
	
	echo '<tr>';
    echo '<td><button type="submit" name="placeOrder">Place Order</button></td>';
    echo '</tr>';

    echo '</table>';
    echo '</form>';
}


?>