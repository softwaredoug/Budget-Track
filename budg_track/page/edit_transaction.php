<?php

include_once('classes/Transaction.php');
include_once('forms/TransactionTableRowForm.php');

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


$page_body = Utility::getFile("template/edit_transaction.html");
$editTransactionFormRow = new TransactionTableRowForm('post', 'index.php?page=edit_transaction&budgetId={MAGIC-BUDGET_UID}&uid=' . $editingTransactionUid);

// form processing
if ($editTransactionFormRow->didUserSubmitInput($page_query['post']))
{
    try 
    {
        $transactionToUpdate = $editTransactionFormRow->GetUserInputTransaction($page_query['post']);
        $transactionToUpdate->SetUid($editingTransactionUid);
        $transactionToUpdate->SetBudgetId($obj['budget']->getUid());
        $obj['tables']['transaction']->update( $transactionToUpdate );
        $page_body = Template::unhideBlock($page_body, 'SUCCESS');
    }
    catch (UserInputException $userInputError)
    {
        $errorHtmlView = $userInputError->GetErrorNoticeView();
        $page_body = Template::unhideBlock($page_body, 'USER_INPUT_INVALID');
        $page_body = Template::replaceValues($page_body, array('USER_INPUT_INVALID_NOTICE' => $errorHtmlView->generateHtml()));
    }

}

// page generation
$transaction = $obj['tables']['transaction']->selectUsingId($editingTransactionUid);
$editTransactionFormRow->SetTransaction($transaction);

$page_body = Template::replaceValues($page_body, array('FORM_ROW' => $editTransactionFormRow->generateHtml(),
                                                       'UID' => $transaction->getUid())); 

$page_result['body'] = $page_body;
$page_result['active_tab'] = 'SELECT_TRANSACTION_OVERVIEW';

?>
