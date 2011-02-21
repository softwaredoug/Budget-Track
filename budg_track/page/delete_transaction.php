<?php

include_once('classes/Transaction.php');
include_once('page/view/TransactionTableView.php');
include_once('page/view/TransactionScheduleControlSequentialView.php');
include_once('php_code_library/template/Template.php');

$page_body = Utility::getFile('template/delete_transaction.html');


$transaction = $obj['tables']['transaction']->selectUsingId(intval($page_query['get']['uid']));

if ($transaction)
{
    $page_body = Template::unhideBlock($page_body, 'TRANSACTION_FOUND');
    $transactionsScheduleView = new TransactionScheduleControlSequentialView($transaction, false);
    $transView = new TransactionTableView($transaction, $transactionsScheduleView, false);
    $page_body = Template::replaceValues($page_body,
                                              array('TRANSACTION_COLS' => $transView->generateHtml(),
                                                    'UID' => $page_query['get']['uid']));

    // If submit has been pressed
    if ($page_query['post']['SubmitForDeletion'])
    {
        if ($obj['tables']['transaction']->delete($transaction))
        {
            $page_body = Template::unhideBlock($page_body, 'ROW_DELETED');
        }
        
    }
    else
    {
        $page_body = Template::unhideBlock($page_body, 'SUBMIT_FOR_DELETION');
    }
}
else
{
    $page_body = Template::unhideBlock($page_body, 'ROW_NOT_FOUND');
}


$page_result['body'] = $page_body;
$page_result['active_tab'] = 'SELECT_TRANSACTION_OVERVIEW';

?>
