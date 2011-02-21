<?php

include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');
include_once('forms/FormHeader.php');
include_once('forms/validators/UserInputTransaction.php');
include_once('classes/Transaction.php');


class TransactionTableRowForm extends HtmlView
{
    var $formHeader;
    var $transaction;

    var $fields;

    function TransactionTableRowForm($method, $action)
    {
        $this->formHeader = new FormHeader($method, $action);
        $this->transaction = null;
    }

    function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    function generateHtml()
    {
        static $transactionTableFormHtml = '';
        if ($transactionTableFormHtml == '')
        {
            $transactionTableFormHtml = Utility::getFile('template/view/transactionTableForm.html');
        }
        // If we are editing something that already exists
        if ($this->transaction != null)
        {
            $generatedHtml = Template::replaceValues($transactionTableFormHtml, 
                        array('NAME' => $this->transaction->getName(),
                              'DESCRIPTION' => $this->transaction->getDescription(),
                              'DOLLAR_AMOUNT' => $this->transaction->getDollarAmount(),
                              'SUBMIT_BUTTON_TEXT' => 'Submit Edits'));  
        }
        // If we are dealing with a new transaction
        else
        {
            $generatedHtml = Template::replaceValues($transactionTableFormHtml, 
                                                     array('SUBMIT_BUTTON_TEXT' => 'Add New Transaction'));
        }
        
        // Add in the form header
        return Template::replaceValues($generatedHtml, array('FORM_HEADER' => $this->formHeader->generateHtml()));
    }

    function didUserSubmitInput($userSentData)
    {
        return isset($userSentData['submit_button']);
    }

    // Throws UserInputException on failure
    function getUserInputTransaction($userSentData)
    {
        $validUserInputTransaction = new UserInputTransaction($userSentData['name'], 
                                                              $userSentData['description'],
                                                              $userSentData['dollar_amount']);
        return $validUserInputTransaction->GetValidTransaction();
    }


}


?>
