<?php

// Code generic for all schedule types for reading schedule
// from a form and inserting it into the specified schedule
// table
class InsertScheduleFromFormTranslator
{
    var $schedTable;
    var $transactionTable;
    var $scheduleForm;

    // throws PageParameterNotFoundException on transactionUid not found in specified table
    // throws IllegalPageRequestException on transactionUid not a numeric value 
    function InsertScheduleFromFormTranslator(DatabaseTable &$schedTable,
                                             // Form setup
                                             $scheduleFormType, $action, $method, 
                                             // Transaction associated with the input schedule
                                             $transactionUid)
    {
        global $obj;
        $this->schedTable =& $schedTable;
        $transactionTable =& $obj['tables']['transaction'];
        
        if (is_numeric($transactionUid) || is_int($transactionUid))
        {
            $transactionUid = intval($transactionUid);
            $transaction = $transactionTable->selectUsingId($transactionUid);
            if ($transaction)
            {
                $this->scheduleForm = new $scheduleFormType($method, $action, $transaction);
            }
            else
            {
                echo 'yo3';
                throw new PageParameterNotFoundException; 
            }
        }
        else
        {
            echo 'yo4';
            throw new IllegalPageRequestException; 
        }
    }
    
    function GetTransaction()
    {
        return $this->scheduleForm->getDisplayedTransaction();
    }

    // throws UserInputException on failure to validate form contents
    function InsertScheduleFromForm($userSentData)
    {
        $userInputSchedule = $this->scheduleForm->ExtractUserInputSchedule($userSentData);
        $transaction = $this->scheduleForm->GetDisplayedTransaction();
        $userInputSchedule->setTransactionUid(intval($transaction->getUid()));
        $this->scheduleForm->SetScheduleToDisplay($userInputSchedule);
        
        $this->schedTable->insert($userInputSchedule);
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
