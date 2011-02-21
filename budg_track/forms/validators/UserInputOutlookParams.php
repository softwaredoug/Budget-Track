<?php

include_once('forms/UserInputException.php');
include_once('forms/validators/regexPatterns.php');


class UserInputOutlookParams
{
    var $startDate;
    var $numDays;
    var $startBalance;

    function UserInputOutlookParams( $startDateStr, $numDaysStr, $startBalance )
    {
        if (!is_numeric($numDaysStr) || $numDaysStr <= 0)
        {
            throw new UserInputException('Number Of Days', 'this field must be a positive integer'); 
        }
        else if (!is_numeric($startBalance))
        {
            throw new UserInputException('Starting Balance', 'this field must be a number'); 
        }
        else if (!IsValidDateYYYYMMDD($startDateStr))
        {
            throw new UserInputException('Start Date', 'this field must be formatted YYYY-MM-DD (like 2008-06-15 for June 15th, 2008)'); 
        }
        else
        {
            $this->startDate = new DateAndTime($startDateStr);
            $this->numDays = intval($numDaysStr);
            $this->startBalance = doubleval($startBalance);
        }
    }

    function GetStartDate()
    {
        return $this->startDate;
    }

    function GetNumDays()
    {
        return $this->numDays;
    }

    function GetStartBalance()
    {
        return $this->startBalance;
    }
}

?>
