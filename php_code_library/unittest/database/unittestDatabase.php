<?php

include_once('database/MySqlConnection.php');


function getUnittestDatabase()
{
    $cfg = array('host' => 'localhost',
                 'user' => 'webuser',
                 'pass' => 'kharabean',
                 'db'  => 'php_code_library_unittest');

    $db = new MySqlConnection($cfg);
    $db->open();
    return $db;

}




?>
