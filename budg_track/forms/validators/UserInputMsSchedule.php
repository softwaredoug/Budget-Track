<?php

include_once('classes/MonthlySchedule.php');
include_once('forms/UserInputException.php');

class UserInputMsSchedule
{
    var $validMsSchedule;

    function UserInputMsSchedule($dayOfMonth)
    {
        if ($dayOfMonth > 28 || $dayOfMonth < -27)
        {
            throw new UserInputException('Day Of Month', 'this field must be between 1-28'); 
        }
        else
        {
            $this->validMsSchedule = new MonthlySchedule();
            $this->validMsSchedule->SetDayOfMonth($dayOfMonth);
        }
    }

    function GetValidMsSchedule()
    {
        return $this->validMsSchedule;
    }
}


?>
