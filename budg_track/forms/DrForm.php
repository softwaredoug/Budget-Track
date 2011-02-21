<?php

include_once('forms/ScheduleForm.php');
include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');
include_once('classes/Transaction.php');
include_once('classes/DaysRepeatedAfterFirstOccurenceSchedule.php');
include_once('forms/validators/UserInputDrSchedule.php');

// Form for Days Repeated after a date schedule
class DrForm extends ScheduleForm 
{

    function DrForm($method, $action, Transaction $transaction)
    {
        $this->ScheduleForm($method, $action, $transaction);
        $this->drSchedule = null;
    }

    function SetScheduleToDisplay(DaysRepeatedAfterFirstOccurenceSchedule $drSchedule )
    {
        $this->drSchedule = $drSchedule;
    }

    function GetDisplayedSchedule()
    {
        return $this->drSchedule;
    }

    function generateHtml()
    {
        static $drFormHtml = '';
        if ($drFormHtml == '')
        {
            $drFormHtml = Utility::getFile('template/view/drForm.html');
        }

        $generatedHtml = Template::replaceValues($drFormHtml, array('FORM_HEADER' => $this->formHeader->generateHtml(),
                                                                    'TRANSACTON_NAME' => $this->transaction->getName()));
        if ($this->drSchedule != null)
        {
            $startDate = $this->drSchedule->getStartDate();
            $startDate = $startDate->toString('Y-m-d');

            $generatedHtml = Template::replaceValues($generatedHtml, array('START_DATE' => $startDate, 
                                                                          'DAYS_REPEATED' => $this->drSchedule->getDaysAfterToRepeat()));
        }
        return $generatedHtml;
    }
    
    function didUserSubmitInput($userSentData)
    {
        return isset($userSentData['submit_button']);
    }
    
    function ExtractUserInputSchedule($userSentData)
    {
        $validUserInputDrSchedule = new UserInputDrSchedule($userSentData['start_date'], 
                                                            $userSentData['days_repeated']);
        return $validUserInputDrSchedule->GetValidDrSchedule();
    }
}


?>
