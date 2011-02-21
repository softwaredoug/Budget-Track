
<?php

include_once('classes/Transaction.php');
include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');


#Generate Html, in the form of a row to be inserted into a table
#for a transaction object
class TransactionTableView extends HtmlView
{
    // Generator of the html for
    var $scheduleView;
    var $transaction;
    var $showControlColumn;

    function TransactionTableView(Transaction &$transactionObj,HtmlView &$scheduleView, $showControlColumn = true)
    {
        $this->scheduleView = $scheduleView;
        $this->transaction = $transactionObj;
        $this->showControlColumn = $showControlColumn;
    }

    function GenerateHtml()
    {
        static $templateBase = '';
        if ($templateBase == '')
        {
            $templateBase = Utility::getFile('template/view/transactionTableView.html');
        }

        if ($this->showControlColumn)
        {
            $transactionHtml = Template::unhideBlock($templateBase, 'CONTROL');
        }
        else
        {
            $transactionHtml = $templateBase;
        }


        return Template::replaceValues($transactionHtml,   array('UID' => $this->transaction->GetUid(),
                                                                 'NAME' => $this->transaction->GetName(),
                                                                 'AMOUNT' => '$' . $this->transaction->GetDollarAmount(),
                                                                 'SCHEDULE' => $this->scheduleView->GenerateHtml()));
    }


}


?>
