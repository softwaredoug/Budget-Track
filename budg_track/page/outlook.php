<?php
include_once('php_code_library/datetime/DateAndTime.php');
include_once('page/view/ScheduledTransactionView.php');
include_once('forms/OutlookForm.php');


$page_body = Utility::getFile('template/outlook.html');

// Order of setting these params
// 1 - from form
// 2 - from cookie
// 3 - from defaults

$defaultStartBalance = 0;
$defaultNumDays = 360;
$now = new DateAndTime();
$userInputExtracted = false;
$sessionInputExtracted = false;

session_start();
// Set outlook params
$outlookParamsForm = new OutlookForm('post', 'index.php?page=outlook&budgetId={MAGIC-BUDGET_UID}');
if (OutlookForm::DidUserSubmitInput( $page_query['post']))
{
    try
    {
        $userInputParams = $outlookParamsForm->ExtractUserInputOutlookParams($page_query['post']);
        $startBalance = $userInputParams->GetStartBalance();
        $numDays = $userInputParams->getNumDays();
        $startDate = $userInputParams->GetStartDate();
        $userInputExtracted = true;
    }
    catch (UserInputException $error)
    {
        $page_body = Template::replaceWithinBlock($page_body, 'USER_INPUT_INVALID', 
                        array('USER_INPUT_INVALID_NOTICE' => $error->GetErrorNoticeView()->generateHtml()));
    }
}
else
{
    if (!empty($_SESSION['start_balance']))
    {
        $startBalance = $_SESSION['start_balance'];

        assert(!empty($_SESSION['start_date']));
        assert(!empty($_SESSION['num_days']));
        
        $startDate = $_SESSION['start_date'];
        $numDays =   $_SESSION['num_days'];
        $sessionInputExtracted = true;
    }
}

if (!$userInputExtracted && !$sessionInputExtracted)
{
    $startBalance = $defaultStartBalance;
    $numDays = $defaultNumDays;
    $startDate = $now;
}

$outlookParamsForm->SetDisplayedStartDate($startDate);
$_SESSION['start_date'] = $startDate;
$outlookParamsForm->SetDisplayedNumDays($numDays);
$_SESSION['num_days'] = $numDays;
$outlookParamsForm->SetDisplayedStartBalance($startBalance);
$_SESSION['start_balance'] = $startBalance;
                                    
$page_body = Template::replaceValues($page_body, array('OUTLOOK_REINIT_FORM'=>$outlookParamsForm->generateHtml(),
                                                        'START_DATE'=>$startDate->toString('Y-m-d'),
                                                        'NUM_DAYS' => $numDays,
                                                        'START_BALANCE' => $startBalance)
                                     );


$page_result['body'] = $page_body;
$page_result['active_tab'] = 'SELECT_OUTLOOK';

?>
