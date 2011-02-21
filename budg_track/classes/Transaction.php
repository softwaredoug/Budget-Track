<?php

include_once('database/DatabaseObject.php');
include_once('classes/DaysRepeatedAfterFirstOccurenceSchedule.php'); 
include_once('classes/MonthlySchedule.php');
include_once('classes/OneTimeSchedule.php');


function ArrayConcat(array $array1, array $array2)
{
    $concatArray = $array1;
    foreach ($array2 AS $array2Item)
    {
        $concatArray[] = $array2Item;
    }
    return $concatArray;
}


class Transaction extends DatabaseObject
{
// Variable names should match what's in the database
    var $uid;
    var $name;
    var $description;
    var $dollar_amount;
    var $budget_id;

    // Types of schedules
    var $dr_sched;
    var $ms_sched;
    var $ot_sched;

    function Transaction()
    {
        $this->DatabaseObject();
        $this->dr_sched = array();
        $this->ms_sched = array();
        $this->ot_sched = array();
    }

    function &getAsRow()
    {
        $row = array();
        $row['uid'] = $this->uid;
        $row['name'] = $this->name;
        $row['description'] = $this->description;
        $row['dollar_amount'] = $this->dollar_amount;
        $row['budget_id'] = $this->budget_id;
        return $row;
    }

    function setFromRow($row)
    {
        if (is_array($row))
        {
            $this->uid = $row['uid'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->dollar_amount = $row['dollar_amount'];
            $this->budget_id = $row['budget_id'];
        }
    }

    function &getDaysRepeatedSchedules()
    {
        return $this->dr_sched;
    }

    function &getMonthlySchedules()
    {
        return $this->ms_sched;
    }

    function &getOneTimeSchedules()
    {
        return $this->ot_sched;
    }

    function getSchedules()
    {
        return ArrayConcat($this->dr_sched, ArrayConcat($this->ms_sched, $this->ot_sched));
    }

    function &getReferrersOfType($typeName)
    {
        if ($typeName == 'DaysRepeatedAfterFirstOccurenceSchedule')
        {
            return $this->getDaysRepeatedSchedules();
        }
        else if ($typeName == 'MonthlySchedule')
        {
            return $this->getMonthlySchedules();
        }
        else if ($typeName == 'OneTimeSchedule')
        {
            return $this->getOneTimeSchedules();
        }
    }

    function SetName($name)
    {
        if (is_string($name))
        {
            $this->name = $name;
        }
    }

    function SetDescription($description)
    {
        if (is_string($description))
        {
            $this->description = $description;
        }
    }

    function SetDollarAmount($dollarAmount)
    {
        if (is_numeric($dollarAmount))
        {
            $this->dollar_amount = $dollarAmount;
        }
    }

    function SetUid($id)
    {
        if (is_int($id))
        {
            $this->uid = $id;
        }
    }

    function SetBudgetId($id)
    {
        if (is_int($id))
        {
            $this->budget_id = $id;
        }
    }

    function GetName()
    {
        return $this->name;
    }

    function GetDescription()
    {
        return $this->description;
    }

    function GetDollarAmount()
    {
        return $this->dollar_amount;
    }

    function GetUid()
    {
        return $this->uid;
    }




}



?>
