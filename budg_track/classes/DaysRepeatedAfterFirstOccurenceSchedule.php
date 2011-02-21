<?php

include_once('database/DatabaseObject.php');
include_once('datetime/DateAndTime.php');
include_once('classes/ISchedule.php');
include_once('utility/DateUtils.php');

class DaysRepeatedAfterFirstOccurenceSchedule extends DatabaseObject implements ISchedule
{
    // Make these the same as specified in database, or implement conversion
    // funcs specified in DatabaseObject
    var $uid;
    var $startDate;
    var $daysAfterToRepeat;
    var $transaction_uid;
    
    function DaysRepeatedAfterFirstOccurenceSchedule()
    {
        $this->DatabaseObject();
    }

    function &getAsRow()
    {
        $row = array();
        $row['uid'] = $this->uid;
        $row['startDate'] = $this->startDate;
        $row['daysAfterToRepeat'] = $this->daysAfterToRepeat;
        $row['transaction_uid'] = $this->transaction_uid;
        return $row;
    }

    function setFromRow($row)
    {
        if (is_array($row))
        {
            $this->uid = $row['uid'];
            $this->startDate = $row['startDate'];
            $this->daysAfterToRepeat = $row['daysAfterToRepeat'];
            $this->transaction_uid = $row['transaction_uid'];
        }
    }

    function getUid()
    {
        return $this->uid;
    }

    function setUid($id)
    {
        if (is_int($id))
        {
            $this->uid = $id;
        }
    }
    
    function setStartDate($dateTimeObj)
    {
        if (is_object($dateTimeObj))
        {
            $this->startDate = $dateTimeObj->toString();
        }
    }

    function getStartDate()
    {
        // Convert to DateAndTime object
        $retVal = new DateAndTime($this->startDate);
        return $retVal;
    }

    function setDaysAfterToRepeat($daysAfterToRepeat)
    {
        $this->daysAfterToRepeat = $daysAfterToRepeat;
    }

    function getDaysAfterToRepeat()
    {
        return $this->daysAfterToRepeat;
    }

    function setTransactionUid($transactionUid)
    {
        if (is_int($transactionUid))
        {
            $this->transaction_uid = $transactionUid;
        }
    }

    function getTransactionUid()
    {
        return $this->transaction_uid;
    }

    function FindNextOccurenceAfterDate(DateAndTime $fromThisDate)
    {
        $dayDistance = DateDiffInDays($fromThisDate, $this->getStartDate());
        if ($dayDistance >= 0)
        {
            // Start date is in the past
            // If we know the distance in days, dividing it will give us the number
            // of whole reoccurences. The remainder of that division is how many days
            // since the last whole period, hence the modulo
            $daysAgoThisLastOccured = ($dayDistance % $this->getDaysAfterToRepeat());
            $daysIntoFuture = ((-$daysAgoThisLastOccured) + $this->getDaysAfterToRepeat());
            $fromThisDate = $fromThisDate->AddDays($daysIntoFuture);
            return $fromThisDate;
        }
        else
        {
            return $this->getStartDate();
        }
    }

}
