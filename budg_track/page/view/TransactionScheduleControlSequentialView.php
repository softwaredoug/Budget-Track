<?php

include_once('classes/Transaction.php');
include_once('page/view/HtmlView.php');

// Problems with this apps architecture
// - It will be difficult to add a new schedule type, each
//   is different and each require its own methods of display/
//   form editing
// - I'm not sure if these class names make sense to anyone

include_once('page/view/OtScheduleControlView.php');
include_once('page/view/DrScheduleControlView.php');
include_once('page/view/MsScheduleControlView.php');
include_once('page/view/ScheduleControlViewUtil.php');
include_once('page/view/SequentiallyDisplayedView.php');
include_once('forms/AddScheduleToTransactionMenu.php');

function CountTransactionsSchedules(Transaction &$transaction)
{
    $msCount = count($transaction->getMonthlySchedules());
    $drCount = count($transaction->getDaysRepeatedSchedules());
    $otCount = count($transaction->getOneTimeSchedules());
    return $msCount + $drCount + $otCount;
}

class TransactionScheduleControlSequentialView extends HtmlView
{
    var $transaction;
    var $showControl;

    function TransactionScheduleControlSequentialView(Transaction &$transaction, $showControl = true)
    {
        $this->transaction = $transaction;
        $this->showControl = $showControl;
    }

    function generateHtml()
    {
        $msCount = count($this->transaction->getMonthlySchedules());
        $otCount = count($this->transaction->getOneTimeSchedules());
        $schedCount = CountTransactionsSchedules($this->transaction); 

        if ($schedCount > 1)
        {
            $schedCount--;
            $bunchOfCommas = array_fill(0, $schedCount, ', ');
            $bunchOfCommas[$schedCount - 1] = ', and ';
        }
        // Generate a list/sequential view of each kind of schedule and append them together

        $showAddWhenSchedulesAlreadyExist = true; 

        $msViews = SchedArrayToSequentiallyDisplayedView($this->transaction->getMonthlySchedules(), 
                                                                           "MsScheduleControlView", 
                                                                           $this->showControl,
                                                                           $bunchOfCommas);
        if (is_array($bunchOfCommas))
        {
            $bunchOfCommas = array_slice($bunchOfCommas, $msCount);
        }
        
        $otViews = SchedArrayToSequentiallyDisplayedView($this->transaction->getOneTimeSchedules(), 
                                                         "OtScheduleControlView",
                                                         $this->showControl,
                                                         $bunchOfCommas);
        if (is_array($bunchOfCommas))
        {
            $bunchOfCommas = array_slice($bunchOfCommas, $otCount);
        }

        $drViews = SchedArrayToSequentiallyDisplayedView($this->transaction->getDaysRepeatedSchedules(), 
                                                         "DrScheduleControlView",
                                                         $this->showControl,
                                                         $bunchOfCommas);
        
        $thisTransactionsSchedView = new SequentiallyDisplayedView();
        $thisTransactionsSchedView->AppendView($msViews);
        $thisTransactionsSchedView->AppendView($otViews);
        $thisTransactionsSchedView->AppendView($drViews);

        return $thisTransactionsSchedView->generateHtml();

    }
}


?>
