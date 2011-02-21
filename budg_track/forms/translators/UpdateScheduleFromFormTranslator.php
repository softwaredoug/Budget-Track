<?php

include_once('php_code_library/database/DatabaseTable.php');
include_once('forms/ScheduleForm.php');
include_once('page/IllegalPageRequestException.php');
include_once('classes/Transaction.php');


// Interface for editing a schedule object of any type
class UpdateScheduleFromFormTranslator
{
    var $schedTable;
    var $transactionTable;
    var $scheduleForm;

    // throws PageParameterNotFoundException on scheduleUid not found in specified table
    // throws IllegalPageRequestException on scheduleUid not a numeric value 
    function UpdateScheduleFromFormTranslator(DatabaseTable &$schedTable, 
                                             // Form setup
                                             $scheduleFormType, $action, $method, 
                                             // Schedule to update
                                             $scheduleUid)
    {
        global $obj;
        $this->schedTable =& $schedTable;
        $transactionTable =& $obj['tables']['transaction'];

        if (is_numeric($scheduleUid) || is_int($scheduleUid))
        {
            $scheduleUid = intval($scheduleUid);
            $schedule = $this->schedTable->selectUsingId($scheduleUid);
            if ($schedule)
            {
                $transaction = $transactionTable->selectUsingId(intval($schedule->getTransactionUid()));
                $this->scheduleForm = new $scheduleFormType($method, $action, $transaction);
                $this->scheduleForm->SetScheduleToDisplay($schedule);
            }
            else
            {
                throw new PageParameterNotFoundException; 
            }

        }
        else
        {
            throw new IllegalPageRequestException; 
        }

    }

    function GetTransaction()
    {
        return $this->scheduleForm->getDisplayedTransaction();
    }

    // throws UserInputException on failure to validate form contents
    function UpdateScheduleFromForm($userSentData)
    {
        $scheduleBeforeUpdate = $this->scheduleForm->GetDisplayedSchedule();

        $userInputSchedule = $this->scheduleForm->ExtractUserInputSchedule($userSentData);
        $userInputSchedule->setUid(intval($scheduleBeforeUpdate->getUid()));
        $userInputSchedule->setTransactionUid(intval($scheduleBeforeUpdate->getTransactionUid()));
        $this->scheduleForm->SetScheduleToDisplay($userInputSchedule);


        $this->schedTable->update($userInputSchedule);
    }

    function DidUserSubmitInput($userSentData)
    {
        return $this->scheduleForm->didUserSubmitInput($userSentData);
    }

    function GenerateHtml()
    {
        return $this->scheduleForm->generateHtml();
    }

}


?>
