<?php

$scheduleType = $page_query['get']['type'];

$page_body = Utility::getFile("template/edit_schedule.html");

// Prepare parameters for the schedule template
// based on the type
if (is_numeric($page_query['get']['uid']))
{
    $editingSchedUid = intval($page_query['get']['uid']);
}
else
{
    throw new IllegalPageRequestException;
}

include_once('classes/Transaction.php');
include_once('forms/translators/UpdateScheduleFromFormTranslator.php');

$getBackHereUrl = 'index.php?page=edit_schedule&budgetId={MAGIC-BUDGET_UID}&type=' . $scheduleType . '&uid=' . $editingSchedUid;
if ($scheduleType == 'ms')
{
    include_once('forms/MsForm.php');
    $formToDbUpdater = new UpdateScheduleFromFormTranslator($obj['tables']['monthlySchedule'],
                                                            'MsForm', $getBackHereUrl, 'post', 
                                                            $page_query['get']['uid']);
}
else if ($scheduleType == 'dr')
{
    include_once('forms/DrForm.php');
    $formToDbUpdater = new UpdateScheduleFromFormTranslator($obj['tables']['daysRepeatedAfterFirstOccurenceSchedule'],
                                                            'DrForm', $getBackHereUrl, 'post', 
                                                            $page_query['get']['uid']);
}
else if ($scheduleType == 'ot')
{
    include_once('forms/OtForm.php');
    $formToDbUpdater = new UpdateScheduleFromFormTranslator($obj['tables']['oneTimeSchedule'],
                                                            'OtForm', $getBackHereUrl, 'post', 
                                                            $page_query['get']['uid']);
}
else
{
    throw new IllegalPageRequestException;
}


$transaction = $formToDbUpdater->getTransaction();


// Check Form
if ($formToDbUpdater->didUserSubmitInput($page_query['post']))
{
    try 
    {
        $formToDbUpdater->UpdateScheduleFromForm($page_query['post']);
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

$page_body = Template::replaceValues($page_body, array('FORM' => $formToDbUpdater->generateHtml(),
                                                       'TRANSACTION_NAME' => $transaction->getName(),
                                                       'TRANSACTION_UID' => $transaction->getUid()));

$page_result['body'] = $page_body;
$page_result['active_tab'] = 'SELECT_TRANSACTION_OVERVIEW';

?>
