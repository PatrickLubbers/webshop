<?php

session_start();

include 'datalayer.php';
include 'businesslayer.php';
include 'presentationlayer.php';
include 'styling.php';

if (isset($user)) {
	$user = $_SESSION['user'];
	show_logout_button();
} else {
	$user = "guest";
}

show_welcome($user);

$connection = connect_to_database(); //connects to database

$userId = get_username_id($connection, $user);

if (isset($_POST['logOut'])) {
	unset($_SESSION['user']); 
}

if (isset($_POST['addToCart'])) {
    $itemId = $_POST['addToCart'];
	add_to_cart($itemId, $userId);
    //insert_into_cart($connection, $itemId, $user, $userId); //inserts into database
}

if (isset($_POST['placeOrder'])) {
	place_order($userId, $user, $connection); 
}

$items = get_items($connection);

show_products($items); //retrieves data from database
show_cart($connection);
show_previous_orders($connection, $user, $userId); //retrieves data from database

mysqli_close($connection);

?>