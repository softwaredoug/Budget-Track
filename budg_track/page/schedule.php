<?php

include_once('classes/Transaction.php');
include_once('page/view/TransactionScheduleControlSequentialView.php');


$editingTransactionUid = -1;
if (is_numeric($page_query['get']['uid']))
{
    $editingTransactionUid = intval($page_query['get']['uid']);
}
else
{
    // giveup TODO
    exit;
}

$page_body = Utility::getFile("template/schedule.html");

$transaction = $obj['tables']['transaction']->selectUsingId($editingTransactionUid);

// Create schedule view
if (CountTransactionsSchedules($transaction) > 0)
{
    $transactionsScheduleView = new TransactionScheduleControlSequentialView($transaction, true);
    $page_body = Template::replaceWithinBlock($page_body, 'SCHEDULES_EXIST',
                                         array('SCHEDULE_CONTROL_VIEW' => $transactionsScheduleView->generateHtml()));
}

$addScheduleView = new AddScheduleToTransactionMenu($transaction);
$page_body = Template::replaceValues($page_body, array('TRANSACTION_NAME'=> $transaction->getName(),
                                                       'ADD_SCHEDULE_VIEW' => $addScheduleView->generateHtml()));


$page_result['body'] = $page_body;

?>
