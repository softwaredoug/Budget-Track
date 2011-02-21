<?php

include_once('classes/DaysRepeatedAfterFirstOccurenceSchedule.php');
include_once('forms/UserInputException.php');
include_once('datetime/DateAndTime.php');
include_once('forms/validators/regexPatterns.php');

class UserInputDrSchedule
{
    var $validDrSchedule;

    function UserInputDrSchedule($startDate, $daysAfterToRepeat)
    {
        if (!is_numeric($daysAfterToRepeat) || $daysAfterToRepeat <= 0)
        {
            throw new UserInputException('Number Of Days', 'this field must be a positive integer'); 
        }
        if (!IsValidDateYYYYMMDD($startDate))
        {
            throw new UserInputException('Start Date', 'this field must be formatted YYYY-MM-DD (like 2008-06-15 for June 15th, 2008)'); 
        }
        else
        {
            $this->validDrSchedule = new DaysRepeatedAfterFirstOccurenceSchedule();
            $this->validDrSchedule->setDaysAfterToRepeat($daysAfterToRepeat);
            $startDateObj = new DateAndTime($startDate);
            $this->validDrSchedule->SetStartDate($startDateObj);
        }
    }

    function GetValidDrSchedule()
    {
        return $this->validDrSchedule;
    }
}


?>
