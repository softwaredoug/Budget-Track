<?php

include_once('database/DatabaseObject.php');
include_once('classes/ISchedule.php');

class MonthlySchedule extends DatabaseObject implements ISchedule
{
    var $uid;
    var $dayOfMonth;
    var $transaction_uid;

    function MonthlySchedule()
    {
        $this->DatabaseObject();
    }

    function GetUid()
    {
        return $this->uid;
    }

    function SetUid($uid)
    {
        if (is_int($uid))
        {
            $this->uid = $uid;
        }
    }

    function SetDayOfMonth($dayOfMonth)
    {
        if (is_numeric($dayOfMonth))
        {
            $dayOfMonth = intval($dayOfMonth);
        }

        if (is_int($dayOfMonth) && $dayOfMonth > 0 && $dayOfMonth < 32)
        {
            $this->dayOfMonth = $dayOfMonth;
        }
    }

    function GetDayOfMonth()
    {
        return $this->dayOfMonth;
    }

    function GetTransactionUid()
    {
        return $this->transaction_uid;
    }

    function SetTransactionUid($uid)
    {
        if (is_int($uid) && $uid > 0)
        {
            $this->transaction_uid = $uid;
        }
    }

    function &getAsRow()
    {
        $row = array();
        $row['uid'] = $this->uid;
        $row['dayOfMonth'] = $this->dayOfMonth;
        $row['transaction_uid'] = $this->transaction_uid;
        return $row;
    }

    function setFromRow($row)
    {
        if (is_array($row))
        {
            $this->uid = intval($row['uid']);
            $this->dayOfMonth = intval($row['dayOfMonth']);
            $this->transaction_uid = $row['transaction_uid'];
        }
    }

    function FindNextOccurenceAfterDate(DateAndTime $fromThisDate)
    {
        $daysInThisMonth = intval($fromThisDate->toString('t'));
        $dayOfMonthToOccur = $this->GetDayOfMonth();
        $inputMonth = $fromThisDate->GetMonth();
        $inputDayOfMonth = $fromThisDate->GetDay();

        if ($dayOfMonthToOccur == 1)
        {
            // Then it can only be next month because we are looking for the NEXT
            // occurence after the specified date, so the next occurence of something occuring
            // on teh 1st of the month with input 1-1-2009 would have to bee 2-1-2009
            $fromThisDate = $fromThisDate->AddDays($daysInThisMonth - $inputDayOfMonth + 1);
            return $fromThisDate;
        }
        else if ($dayOfMonthToOccur <= 0)
        {
            // 0 == last day of month, -1 == next to last day, etc
            // Adjust to the negative offset from the front of the month 
            $dayOfMonthToOccur = $daysInThisMonth + $dayOfMonthToOccur;
        }

        assert(($dayOfMonthToOccur <= 31) && ($dayOfMonthToOccur > -28));

        if ($inputDayOfMonth >= $dayOfMonthToOccur)
        {
            // Pop to the first of next month. 
            // Need to recurse and look into next month
            $fromThisDate = $fromThisDate->AddDays($daysInThisMonth - $inputDayOfMonth + 1);
            return $this->FindNextOccurenceAfterDate($fromThisDate);
        }
        $fromThisDate = $fromThisDate->AddDays( $dayOfMonthToOccur - $inputDayOfMonth ); 
        return $fromThisDate;
    }

}


?>
