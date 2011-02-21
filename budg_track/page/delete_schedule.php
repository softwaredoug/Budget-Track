<?php

include_once('page/GetScheduleViewAndTable.php');

$scheduleType = $page_query['get']['type'];
$uid = $page_query['get']['uid'];
$dbTable = 0;

$page_body = Utility::getFile("template/delete_schedule.html");
$view = GetScheduleViewAndTable($scheduleType, $uid, $dbTable, false);


if ($view)
{
    $sched = $view->GetDisplayedSchedule();
    $transactionUid = intval($sched->GetTransactionUid());
    $transaction = $obj['tables']['transaction']->selectUsingId($transactionUid);


    if ($page_query['post']['submit_for_deletion'])
    {
        $page_body = Template::unhideBlock($page_body, 'SUCCESS');
        $dbTable->delete($view->GetDisplayedSchedule());
    }
    else
    {
        $page_body = Template::unhideBlock($page_body, 'SUBMIT_FOR_DELETION');
    }
    $page_body = Template::unhideBlock($page_body, 'SCHEDULE_FOUND');
    $page_body = Template::replaceValues($page_body, array('SCHEDULE_VIEW' => $view->generateHtml(),
                                                           'SCHEDULE_TYPE' => $scheduleType,
                                                           'TRANSACTION_NAME' => $transaction->getName(),
                                                           'TRANSACTION_UID' => $transaction->getUid(),
                                                           'UID' => $uid));

}

$page_result['body'] = $page_body;
$page_result['active_tab'] = 'SELECT_TRANSACTION_OVERVIEW';

?>
