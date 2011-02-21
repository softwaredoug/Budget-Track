<?php

include_once('database/DatabaseObject.php');
include_once('datetime/DateAndTime.php');
include_once('classes/ISchedule.php');

class OneTimeSchedule extends DatabaseObject implements ISchedule
{
    var $uid;
    var $dateOfTransaction;
    var $transaction_uid;

    function OneTimeSchedule()
    {
        $this->DatabaseObject();
    }

    function GetUid()
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

    function GetDateOfTransaction()
    {
        $dateTime = new DateAndTime($this->dateOfTransaction);
        return $dateTime;
    }

    function GetTransactionUid()
    {
        return $this->transaction_uid;
    }

    function SetTransactionUid($transaction_uid)
    {
        if (is_int($transaction_uid) && $transaction_uid > 0)
        {
            $this->transaction_uid = $transaction_uid;
        }
    }

    function SetDateOfTransaction($dateOfTransaction)
    {
        if (is_object($dateOfTransaction))
        {
            $this->dateOfTransaction = $dateOfTransaction->toString();
        }
    }

    function &getAsRow()
    {
        $row = array();
        $row['uid'] = $this->uid;
        $row['dateOfTransaction'] = $this->dateOfTransaction;
        $row['transaction_uid'] = $this->transaction_uid;
        return $row;
    }

    function setFromRow($row)
    {
        if (is_array($row))
        {
            $this->uid = intval($row['uid']);
            $this->dateOfTransaction = $row['dateOfTransaction'];
            $this->transaction_uid = $row['transaction_uid'];
        }
    }
    
    function FindNextOccurenceAfterDate( DateAndTime $afterThisDate )
    {
        $dateOfTransaction = $this->GetDateOfTransaction();
        if ($dateOfTransaction->compareTo($afterThisDate) > 0)
        {
            return $dateOfTransaction;
        }
        else
        {
            // This won't occur in the future
            return $afterThisDate;
        }
    }
}

?>
