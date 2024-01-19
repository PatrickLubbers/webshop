<?php

session_start();
include 'functions.php';

$user = $_SESSION['user'];
$items = get_items($connection);

echo "welcome $user!";
echo "<br><br>";

$connection = connect_to_database(); //connects to database
$query = "SELECT * FROM items"; 
//^^Retrieves items from database to display items from cart. Pretty sure it should be run as a function in functions.php
$result = mysqli_query($connection, $query);
$userId = get_username_id($connection, $user);

if (isset($_POST['addToCart'])) {
    $itemId = $_POST['addToCart'];
    insert_into_cart($connection, $itemId, $user, $userId); //inserts into database
}

show_shopping_page($items); //retrieves data from database
show_what_is_in_cart($connection, $user, $userId); //retrieves data from database

mysqli_close($connection);


//the errors indeed came from some refactoring efforts renaming variables $dbConnection to $connection
//but in this file $connection was not defined yet. What is better, defining this variable in here 
//or in the functions table? In function.php would enable me to cut out 1 line of code from both shoppingcartA.php and username.php

//the errors indeed came from some refactoring efforts renaming variables $dbConnection to $connection
//but in this file $connection was not defined yet. What is better, defining this variable in here 
//or in the functions table? In function.php would enable me to cut out 1 line of code from both shoppingcartA.php and username.php
?>
