<?php

include_once('page/view/HtmlView.php');
include_once('classes/Transaction.php');
include_once('php_code_library/datetime/DateAndTime.php');
include_once('php_code_library/utility/Utility.php');

class ScheduledTransactionView extends HtmlView
{
    var $transaction;
    var $date;
    var $resultingBalance;

    function ScheduledTransactionView( Transaction &$transaction,
                                       DateAndTime $when,
                                       $resultingBalance)
    {
        $this->transaction = $transaction;
        $this->date = $when;
        $this->resultingBalance = $resultingBalance;
    }


    function generateHtml()
    {
        static $schedViewTemplate = '';
        if ($schedViewTemplate == '')
        {
            $schedViewTemplate = Utility::getFile('template/view/scheduledTransactionTableRow.html');
        }

        return Template::replaceValues($schedViewTemplate, array('DATE' => $this->date->toString('Y-m-d'),
                                                                'TRANSACTION_NAME' => $this->transaction->getName(),
                                                                'DOLLAR_AMOUNT' => $this->transaction->getDollarAmount(),
                                                                'BALANCE' => $this->resultingBalance));
            
    }
}

?>
