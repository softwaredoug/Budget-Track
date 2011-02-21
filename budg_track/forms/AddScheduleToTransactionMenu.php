<?php

include_once('classes/Transaction.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');
include_once('forms/Form.php');

class AddScheduleToTransactionMenu extends Form
{
    var $transaction;
    function AddScheduleToTransactionMenu(Transaction &$transaction)
    {
        $this->Form('get', 'index.php?page=add_schedule');
        $this->transaction =& $transaction;
    }

    function generateHtml()
    {
        static $addScheduleLinkBarHtml = '';
        if ($addScheduleLinkBarHtml == '')
        {
            $addScheduleLinkBarHtml = Utility::getFile('template/view/addScheduleToTransactionLinkBar.html');
        }
        return Template::replaceValues($addScheduleLinkBarHtml, 
                                        array('TRANSACTION_UID' => $this->transaction->getUid(),
                                              'FORM_HEADER' => $this->formHeader->generateHtml()));
    }
}




?>
