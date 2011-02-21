<?php

include_once('page/view/HtmlView.php');
include_once('classes/Transaction.php');
include_once('php_code_library/template/Template.php');
include_once('php_code_library/utility/Utility.php');

class ClickToScheduleTransactionView extends HtmlView
{
    var $transaction;

    function ClickToScheduleTransactionView(Transaction &$transaction)
    {
        $this->transaction = $transaction;
    }

    function GenerateHtml()
    {
        static $clickToScheduleTemplate = '';
        if ($clickToScheduleTemplate == '')
        {
            $clickToScheduleTemplate = Utility::getFile('template/view/clickToSchedule.html'); 
        }
        return Template::replaceValues($clickToScheduleTemplate, 
                                            array('TRANSACTION_NAME' => $this->transaction->getName(),
                                                  'TRANSACTION_UID' => $this->transaction->getUid()));
    }
}



?>
