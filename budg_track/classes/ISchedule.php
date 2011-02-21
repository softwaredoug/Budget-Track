<?php

include_once('datetime/DateAndTime.php');

// Schedule interface
interface ISchedule
{
    // Takes a date time and returns a date time with the next occurence
    // after the input this will occur. If it will never occur, returns
    // date time identical to the input
    function FindNextOccurenceAfterDate( DateAndTime $afterThisDate );
}



?>
