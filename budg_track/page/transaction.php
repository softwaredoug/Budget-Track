<?php

include_once('classes/Transaction.php');
include_once('page/view/TransactionTableView.php');
include_once('page/view/TransactionScheduleControlSequentialView.php');
include_once('page/view/ClickToScheduleTransactionView.php');
include_once('forms/TransactionTableRowForm.php');

$addTransactionFormRow = new TransactionTableRowForm('post', 'index.php?page=transaction&budgetId={MAGIC-BUDGET_UID}'); 

$page_body = Utility::getFile('template/transaction.html');
$page_body = Template::replaceValues($page_body, array('FORM_ROW' => $addTransactionFormRow->generateHtml()));

if ($addTransactionFormRow->didUserSubmitInput($page_query['post']))
{
    try
    {
        $transaction = $addTransactionFormRow->GetUserInputTransaction($page_query['post']);
        $transaction->SetBudgetId($obj['budget']->getUid());
        $obj['tables']['transaction']->insert($transaction);
        $page_body = Template::replaceWithinBlock($page_body, 'SCHEDULE_PROMPT', 
                                                    array('TRANSACTION_NAME' => $transaction->getName(),
                                                          'TRANSACTION_UID' => $transaction->getUid() ));
    }
    catch (UserInputException $error)
    {
        $page_body = Template::unhideBlock($page_body, 'USER_INPUT_INVALID');
        $errorHtmlView = $error->GetErrorNoticeView();
        $page_body = Template::replaceValues($page_body, array('USER_INPUT_INVALID_NOTICE' => $errorHtmlView->generateHtml()));
    }
}

/* Display all transactions with all possible schedule information about them*/
$transactions = $obj['budget']->getTransactions();

// Make a row for each transaction
foreach ($transactions AS $transaction)
{
    // Generate the description of the schedule for each transaction
    if (CountTransactionsSchedules($transaction) >= 1)
    {
        $transactionsScheduleView = new TransactionScheduleControlSequentialView($transaction, false);
    }
    else
    {
        $transactionsScheduleView = new ClickToScheduleTransactionView($transaction);
    }
    $transView = new TransactionTableView($transaction, $transactionsScheduleView);
    $page_body = Template::replaceWithinBlock($page_body, 'TRANSACTION_ROW', 
                                              array('TRANSACTION_TEXT' => $transView->generateHtml()));
}

$page_result['body'] = $page_body;
$page_result['active_tab'] = 'SELECT_TRANSACTION_OVERVIEW';


?>
