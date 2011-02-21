<?php


function IsValidDateYYYYMMDD($dateStr)
{
    // developed with the help of http://regexpal.com
    $dateRegex = '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';
    return (ereg($dateRegex, $dateStr));
}


?>
