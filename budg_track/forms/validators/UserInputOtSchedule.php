<?php

include_once('classes/OneTimeSchedule.php');
include_once('forms/UserInputException.php');
include_once('datetime/DateAndTime.php');
include_once('forms/validators/regexPatterns.php');

class UserInputOtSchedule
{
    var $validOtSchedule;

    function UserInputOtSchedule($dateOfTransaction)
    {
        if (!IsValidDateYYYYMMDD($dateOfTransaction))
        {
            echo 'here';
            throw new UserInputException('Date Of Transaction', 'this field must be formatted YYYY-MM-DD (like 2008-06-15 for June 15th, 2008)'); 
        }
        else
        {
            $this->validOtSchedule = new OneTimeSchedule();
            $dateOfTransactionObj = new DateAndTime($dateOfTransaction);
            $this->validOtSchedule->SetDateOfTransaction($dateOfTransactionObj);
        }
    }

    function GetValidOtSchedule()
    {
        return $this->validOtSchedule;
    }
}


?>
