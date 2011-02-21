<?php

include_once('forms/ScheduleForm.php');
include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');
include_once('classes/Transaction.php');
include_once('classes/MonthlySchedule.php');
include_once('forms/validators/UserInputMsSchedule.php');

// Form for Days Repeated after a date schedule
class MsForm extends ScheduleForm 
{
    var $msSchedule;

    function MsForm($method, $action, Transaction $transaction)
    {
        $this->ScheduleForm($method, $action, $transaction);
        $this->msSchedule = null;
    }

    function SetScheduleToDisplay(MonthlySchedule $msSchedule )
    {
        $this->msSchedule = $msSchedule;
    }

    function GetDisplayedSchedule()
    {
        return $this->msSchedule;
    }

    function generateHtml()
    {
        static $MsFormHtml = '';
        if ($MsFormHtml == '')
        {
            $MsFormHtml = Utility::getFile('template/view/msForm.html');
        }

        $generatedHtml = Template::replaceValues($MsFormHtml, array('FORM_HEADER' => $this->formHeader->generateHtml(),
                                                                    'TRANSACTON_NAME' => $this->transaction->getName()));
        if ($this->msSchedule != null)
        {
            $dayOfMonth = $this->msSchedule->GetDayOfMonth();

            $generatedHtml = Template::replaceValues($generatedHtml, array('DAY_OF_MONTH' => $dayOfMonth));
        }
        return $generatedHtml;
    }
    
    function didUserSubmitInput($userSentData)
    {
        return isset($userSentData['submit_button']);
    }
    
    function ExtractUserInputSchedule($userSentData)
    {
        $validUserInputMsSchedule = new UserInputMsSchedule($userSentData['day_of_month']); 
        return $validUserInputMsSchedule->GetValidMsSchedule();
    }
}


?>
