<?php

include_once('classes/Transaction.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');
include_once('page/view/HtmlView.php');

class AddScheduleToTransactionLinkBarView extends HtmlView
{
    var $transaction;
    function AddScheduleToTransactionLinkBarView(Transaction &$transaction)
    {
        $this->transaction =& $transaction;
    }

    function generateHtml()
    {
        static $addScheduleLinkBarHtml = '';
        if ($addScheduleLinkBarHtml == '')
        {
            $addScheduleLinkBarHtml = Utility::getFile('template/view/addScheduleToTransactionLinkBar.html');
        }
        return Template::replaceValues($addScheduleLinkBarHtml, array('TRANSACTION_UID' => $this->transaction->getUid()));
    }
}




?>
