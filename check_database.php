<?php

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

$connection = connect_to_database();

var_dump($connection);

function get_user_data($connection) {
    $query = "SELECT * FROM username";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die("Error executing query: " . mysqli_error($connection));
    }

    $userData = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $userData[] = $row;
    }

    return $userData;
}

// Assuming $connection is already established
$userData = get_user_data($connection);

// Display the user data
foreach ($userData as $user) {
    echo "Username: " . $user['username'] . "<br>";
    // Add other fields as needed
}

?>