<?php
# Display the control view (shows edit/delete links) of a One-Time schedule
# <X> <E> Occurs on the N(rd/th/..) day of every month
#

include_once('page/view/ScheduleControlViewUtil.php');
include_once('page/view/HtmlView.php');
include_once('classes/MonthlySchedule.php');
include_once('php_code_library/datetime/DateAndTime.php');
include_once('php_code_library/utility/Utility.php');


function int_div($op1, $op2)
{
    if (is_int($op1) && is_int($op2))
    {
        return ($op1 - ($op1 % $op2)) / $op2;
    }
}



function getTensDigit($val)
{
    if (is_int($val))
    {
        $valMod100 = $val % 100;
        return int_div($valMod100, 10);
    }
}



function get1st2ndOrNrd($val)
{
    $rVal = '';
    if (is_int($val))
    {
        $rVal .= $val;
        if (getTensDigit($val) == 1)
        { // 10th, 11th, 12th, 13th, 14th ...
            $rVal .= 'th';
        }
        else
        {
            $valMod10 = $val % 10;
            if ($valMod10 == 1)
            {
                $rVal .= 'st';
            }
            else if ($valMod10 == 2)
            {
                $rVal .= 'nd';
            }
            else if ($valMod10 == 3)
            {
                $rVal .= 'rd';
            }
            else
            {
                $rVal .= 'th';
            }
        }
              
    }
    return $rVal;
}


function getNegativeDaysOfMonthText($daysOfMonth)
{
    $rval = '';
    if (is_int($daysOfMonth))
    {
        if ($daysOfMonth == 0)
        {
            $rval = 'last';
        }
        else if ($daysOfMonth == -1)
        {
            $rval = 'second to last';
        }
        else if ($daysOfMonth == -2)
        {
            $rval = 'third to last';
        }
        else
        {
            $rval = $daysOfMonth;
        }
    }
    return $rval;
}


class MsScheduleControlView extends HtmlView
{
    var $msSchedule;
    var $showSchedule;

    function MsScheduleControlView(&$msSchedule, $showControl = true)
    {
        $this->showControl = $showControl;
        $this->msSchedule = &$msSchedule;
    }

    function GetDisplayedSchedule()
    {
        return $this->msSchedule;
    }

    function GenerateHtml()
    {
        if ($this->msSchedule->getDayOfMonth() > 0)
        {
            $nthText =get1st2ndorNrd($this->msSchedule->getDayOfMonth() ); 
        }
        else
        {
            $nthText = getNegativeDaysOfMonthText( intval($this->msSchedule->getDayOfMonth()));
        }
        $description = 'on the ' . $nthText . ' day of every month';
        return FillScheduleControlViewTemplate($this->msSchedule->getUid(), 'ms', $description, $this->showControl);
    }

}




?>
