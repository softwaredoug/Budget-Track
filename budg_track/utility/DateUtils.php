<?php

include_once('php_code_library/datetime/DateAndTime.php');

function DateDiffInDays(DateAndTime $t1, DateAndTime $t2)
{
    return (floor($t1->toDaysSinceEpoch()) - floor($t2->toDaysSinceEpoch()));
}





?>
