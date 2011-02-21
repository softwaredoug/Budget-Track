<?php

include_once('forms/ScheduleForm.php');
include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');
include_once('classes/Transaction.php');
include_once('classes/OneTimeSchedule.php');
include_once('forms/validators/UserInputOtSchedule.php');

// Form for Days Repeated after a date schedule
class OtForm extends ScheduleForm 
{
    var $otSchedule;

    function OtForm($method, $action, Transaction $transaction)
    {
        $this->ScheduleForm($method, $action, $transaction);
        $this->otSchedule = null;
    }

    function SetScheduleToDisplay(OneTimeSchedule $otSchedule )
    {
        echo 'here';
        $this->otSchedule = $otSchedule;
    }

    function GetDisplayedSchedule()
    {
        return $this->otSchedule;
    }

    function generateHtml()
    {
        static $OtFormHtml = '';
        if ($OtFormHtml == '')
        {
            $OtFormHtml = Utility::getFile('template/view/otForm.html');
        }

        $generatedHtml = Template::replaceValues($OtFormHtml, array('FORM_HEADER' => $this->formHeader->generateHtml(),
                                                                    'TRANSACTON_NAME' => $this->transaction->getName()));
        if ($this->otSchedule != null)
        {
            $dateOfTransaction = $this->otSchedule->GetDateOfTransaction();
            $dateOfTransaction = $dateOfTransaction->toString('Y-m-d');

            $generatedHtml = Template::replaceValues($generatedHtml, array('DATE' => $dateOfTransaction));
        }
        return $generatedHtml;
    }
    
    function didUserSubmitInput($userSentData)
    {
        return isset($userSentData['submit_button']);
    }
    
    function ExtractUserInputSchedule($userSentData)
    {
        $validUserInputOtSchedule = new UserInputOtSchedule($userSentData['date']); 
        return $validUserInputOtSchedule->GetValidOtSchedule();
    }
}


?>
