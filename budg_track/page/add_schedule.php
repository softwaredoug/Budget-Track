<?php

$scheduleType = $page_query['get']['type'];
$page_body = Utility::getFile("template/edit_schedule.html");

if (is_numeric($page_query['get']['transaction_uid']))
{
    $associatedTransactionUid = intval($page_query['get']['transaction_uid']);
}
else
{
    echo 'yo';
    throw new IllegalPageRequestException;
}

$transaction = $obj['tables']['transaction']->selectUsingId($associatedTransactionUid);

include_once('forms/translators/InsertScheduleFromFormTranslator.php');
$getBackHereUrl = 'index.php?page=add_schedule&budgetId={MAGIC-BUDGET_UID}&type=' . $scheduleType . '&transaction_uid=' . $associatedTransactionUid;
if ($scheduleType == 'ms')
{
    include_once('forms/MsForm.php');
    $formToDbInserter = new InsertScheduleFromFormTranslator($obj['tables']['monthlySchedule'],
                                                            'MsForm', $getBackHereUrl, 'post', 
                                                            $associatedTransactionUid);
}
else if ($scheduleType == 'dr')
{
    include_once('forms/DrForm.php');
    $formToDbInserter = new InsertScheduleFromFormTranslator($obj['tables']['daysRepeatedAfterFirstOccurenceSchedule'],
                                                            'DrForm', $getBackHereUrl, 'post', 
                                                            $associatedTransactionUid);
}
else if ($scheduleType == 'ot')
{
    include_once('forms/OtForm.php');
    $formToDbInserter = new InsertScheduleFromFormTranslator($obj['tables']['oneTimeSchedule'],
                                                            'OtForm', $getBackHereUrl, 'post', 
                                                            $associatedTransactionUid);
}
else
{
    echo 'yo2';
    throw new IllegalPageRequestException;
}

$transaction = $formToDbInserter->getTransaction();

// Check form
if ($formToDbInserter->didUserSubmitInput($page_query['post']))
{
    try 
    {
        $formToDbInserter->InsertScheduleFromForm($page_query['post']);
        $page_body = Template::unhideBlock($page_body, 'SUCCESS');
    }
    catch (UserInputException $userInputError)
    {
        // populate error complaining about bad user input
        $errorHtmlView = $userInputError->GetErrorNoticeView();
        $page_body = Template::unhideBlock($page_body, 'USER_INPUT_INVALID');
        $page_body = Template::replaceValues($page_body, 
                                             array('USER_INPUT_INVALID_NOTICE' => $errorHtmlView->generateHtml()));
    }
}

$page_body = Template::replaceValues($page_body, array('FORM' => $formToDbInserter->generateHtml(),
                                                       'TRANSACTION_NAME' => $transaction->getName(),
                                                       'TRANSACTION_UID' => $transaction->getUid()));
$page_result['body'] = $page_body;
$page_result['active_tab'] = 'SELECT_TRANSACTION_OVERVIEW';

?>
