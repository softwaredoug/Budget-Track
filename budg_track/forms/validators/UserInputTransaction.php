<?php

include_once('classes/Transaction.php');
include_once('page/view/NoticeView.php');
include_once('forms/UserInputException.php');


class UserInputTransaction
{
    var $validTransaction;

    // constructor...
    function UserInputTransaction($name, $description, $dollar_amount)
    {
        $this->validTransaction = false;
        if (!is_string($name) || (strlen($name) <= 0) || (strlen($name) > 32) )
        {
            throw new UserInputException('Name', 'this field is required and cannot exceed 32 characters'); 
        }
        else if (!is_numeric($dollar_amount))
        {
            throw new UserInputException('Dollar Amount', 'this field must be a numeric value'); 
        }
        else
        {
            // input is valid, create the transaction object
            $this->validTransaction = new Transaction;
            $this->validTransaction->SetName($name);
            $this->validTransaction->SetDescription($description);
            $this->validTransaction->SetDollarAmount($dollar_amount);
        }
    }

    function GetValidTransaction()
    {
        return $this->validTransaction;
    }
}



?>
