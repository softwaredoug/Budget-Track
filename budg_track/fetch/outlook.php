<?php

include_once('forms/validators/UserInputOutlookParams.php');
include_once('php_code_library/datetime/DateAndTime.php');
include_once('controllers/Outlook.php');

// Access required get values:

try 
{
    $restParams = new UserInputOutlookParams($page_query['get']['start_date'], $page_query['get']['num_days'], $page_query['get']['starting_balance']);
    // $obj['budget'] should also be filled in
    $startBalance = $restParams->GetStartBalance();
    $numDays = $restParams->getNumDays();
    $startDate = $restParams->GetStartDate();

    $outlook = new Outlook($obj['budget'], $startDate, $numDays, $startBalance);
    $outlook->CalculateOutlook(); 

    echo json_encode($outlook->GetScheduledTransactionOccurences());

}
catch (UserInputException $error)
{
    echo "User Input Invalid:";
    print_r($error);
    exit();
}







?>
