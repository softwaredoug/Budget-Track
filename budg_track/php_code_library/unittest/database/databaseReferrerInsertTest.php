
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

$phoneNumTable = new DatabaseTable(     // This second table has a relationship with the 
                        $obj['db'],     // users table, the phone num table points to 
                        't_phonenum',   // a member through one of its columns. 
                                        // Here, we've decided to imply that this
                        'PhoneNum',     // means that the user class has ownership of the 
                        'c_uid');       // phone numbers that refer to it

// Setup the link between the two tables, now the User object should have added a
// "fetReferrerByType". This functions will return a reference to a list of objects that the table can access/setup.
// So when the User object is called with getReferrerOfType with type = PhoneNum, then it will know to return a reference
// to the  the phone numbers owned.
$usersTable->AddReferrer($phoneNumTable, 'c_user'); // Let the table object know about the relationship


// Notice tables perform database operations, and the objects are just dumb collections of data
// First insert a user

// Insert username = $argv[1] and password = $argv[2] and phone number $argv[3]

echo "Test Proceeding with: " . $argv[1] . "," . $argv[2] . "," . $argv[3] . "\n";
$argv[3] = intval($argv[3]);
if (is_string($argv[1]) && is_string($argv[2]) && ($argv[3] != 0))
{


    $user = new User();
    $user->SetUsername($argv[1]);
    $user->SetPassword($argv[2]);


    // Add many phonenums
    for ($i = $argv[3]; $i < ($argv[3] + 7); ++$i)
    {
        $phoneNum = new PhoneNum();
        $phoneNum->SetPhoneNum($i);
        $user->addPhoneNum($phoneNum);
    }

    $usersTable->insert($user);

    // Now lets try to access that inserted item
    $userSelectSet = $usersTable->selectWhere("c_username = '$argv[1]'");

    foreach ($userSelectSet AS $userAgain)
    {
        echo "Id: " . $userAgain->GetUid() ."Username: " .  $userAgain->GetUsername() . " Password:" . $userAgain->GetPassword() . "\n";
        foreach ($userAgain->getPhoneNums() AS $phoneNum)
        {
            echo ">Phone Num = " . $phoneNum->getPhoneNum() . "\n";
        }
    }
}
else
{
    echo "Invalid args\n";
}




?>
