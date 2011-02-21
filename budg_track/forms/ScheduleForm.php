<?php

include_once('forms/Form.php');

class ScheduleForm extends Form
{
    var $schedule;
    var $transaction;

    function ScheduleForm($method, $action, Transaction $transaction)
    {
        $this->Form($method, $action);
        $this->transaction = $transaction;
    }

    function GetDisplayedTransaction()
    {
        return $this->transaction;
    }

    function GetDisplayedSchedule()
    {
    }

    //function SetScheduleToDisplay( typesafe)

    function ExtractUserInputSchedule()
    {
    }


}



?>
