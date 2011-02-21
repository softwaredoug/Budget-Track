<?php

include_once('php_code_library/datetime/DateAndTime.php');
include_once('utility/DateUtils.php');

// Encapsulates the outlook algorithm
//  The outlook algorithm works by calculating, for a range of dates, what date(s)
//  a transaction will occur in that range. It stores them in an associative array
//  keyed on the date in epoch time. In the case of overlapping dates, the
//  key is incremented by 1 (ie 1 second), allowing the date to be identical with
//  different keys.
class Outlook
{
    // User inputs to the algorithm
    var $budget;
    var $numDays;
    var $startDate;
    var $startingBalance;

    // output
    var $scheduledTransactionOccurences;

    // constructor
    function Outlook(SavedBudget $budget, DateAndTime $startDate, $numDays, $startingBalance)
    {
        if ($numDays < 0)
        {
            trigger_error("Class outlook was passed negative numDays. Value: $numDays");
        }
        // should be ok for starting balance to be negative
        $this->budget = $budget;
        $this->numDays = $numDays;
        $this->startDate = $startDate;
        $this->startingBalance = $startingBalance;

        $this->scheduledTransactionOccurences = array();

    }

    // Get the flat array of when each will occur
    function GetScheduledTransactionOccurences()
    {
        return $this->scheduledTransactionOccurences;
    }

    // Compute the complete outlook
    // this involves scheduling every transaction occurence
    // and tabulating the resulting balance of each occurence
    function CalculateOutlook()
    {
        $this->ScheduleTransactionOccurences();
        $this->CalculateResultingBalances();
    }
    
    // Used to compare two transaction occurences by date
    static function TransactionOccurenceDateCompare($a, $b)
    {
        $epochTimeA = $a['epoch_time'];
        $epochTimeB = $b['epoch_time'];
        if ($epochTimeA == $epochTimeB)
        {
            return 0;
        }
        return ($epochTimeA < $epochTimeB) ? -1 : 1;
    }

    // Figure out when each transaction will occur within the dates specified,
    // sort by date
    function ScheduleTransactionOccurences()
    {
        $endDate = $this->startDate->AddDays( $this->numDays );
        $allTransactions = $this->budget->getTransactions();
        $timeCursor = $this->startDate; 
        $scheduledTransactions = array();
        foreach ($allTransactions AS $transaction)
        {
            $scheds = $transaction->getSchedules();
            foreach ($scheds AS $schedule)
            {
                // FindNextOccurence from the start date through the user-specified period
                while ($endDate->compareTo($timeCursor) >= 0)
                {
                    $oldTimeCursor = $timeCursor;
                    
                    $timeCursor = $schedule->FindNextOccurenceAfterDate( $timeCursor );

                    // why would timeCursor ever == oldTimeCursor?
                    if (($timeCursor == $oldTimeCursor) || ($endDate->compareTo($timeCursor) < 0))
                    {
                        break;
                    }

                    // Append this occurence of the transaction away. 
                    $this->StoreTransactionOccurence($timeCursor, $transaction);
                }
                $timeCursor = $this->startDate;

            }

        }

        // Sort based on the transaction occurence date, earliest->latest
        usort($this->scheduledTransactionOccurences, "Outlook::TransactionOccurenceDateCompare");

    }

    // Figure out the resulting balance
    // sort by date
    function CalculateResultingBalances()
    {
        $balance = $this->startingBalance;
        foreach ($this->scheduledTransactionOccurences AS &$transactionOccurence)
        {
            $balance += $transactionOccurence['dollar_amount'];
            $transactionOccurence['resulting_balance'] = $balance;
        }
    }

    // Append transaction occurence to our object's flat array
    function StoreTransactionOccurence(DateAndTime $occurenceDate, $transaction)
    {
        $occurenceEpochTime = $occurenceDate->toSecondsSinceEpoch();
        $this->scheduledTransactionOccurences[] = 
            array("epoch_time" =>    $occurenceEpochTime, 
                  "date" =>          $occurenceDate->toString("Y-m-d"), 
                  "name" =>          $transaction->getName(), 
                  "dollar_amount" => $transaction->getDollarAmount());
    }


}

?>
