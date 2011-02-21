<?php

set_include_path('.:../..');
 
include_once('classes/User.php');
include_once('database/DatabaseTable.php');
include_once('unittestDatabase.php');

// Connect to the database, use the connection whenever a database 
// needs to be specified in an operation

$obj['db'] = getUnittestDatabase();


// Create a database table, tables are used to perform operations
// on the database and create database objects. DatabaseObjects are
// simple POD types that have no knowledge of the database. They only
// inherit from DatabaseObject to allow the DatabaseTable to set/read
// its members.

$usersTable = new DatabaseTable(
                        $obj['db'], // Specifiy database connection, the database where the table exists
                        't_user',   // Specifiy the name of the table
                        'User',     // Specify the name of the DatabaseObject to use
                        'c_uid');   // Specify the table's primary key


// Notice tables perform database operations, and the objects are just dumb collections of data
// First insert a user

// Insert username = $argv[1] and password = $argv[2]

if (is_string($argv[1]) && is_string($argv[2]))
{

    $user = new User();
    $user->SetUsername($argv[1]);
    $user->SetPassword($argv[2]);

    $usersTable->insert($user);

    // Now lets try to access that inserted item
    $userSelectSet = $usersTable->selectWhere("c_username = '$argv[1]'");

    foreach ($userSelectSet AS $userAgain)
    {
        echo "Username: " .  $userAgain->GetUsername() . " Password:" . $userAgain->GetPassword() . "\n";
    }
}




?>
