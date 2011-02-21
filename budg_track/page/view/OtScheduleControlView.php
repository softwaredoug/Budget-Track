<?php
# Display the control view (shows edit/delete links) of a One-Time schedule
# <X> <E> Occurs on (date)
#

include_once('page/view/ScheduleControlViewUtil.php');
include_once('page/view/HtmlView.php');
include_once('classes/OneTimeSchedule.php');
include_once('php_code_library/datetime/DateAndTime.php');

class OtScheduleControlView extends HtmlView
{
    var $otSchedule;
    var $showControl;

    function OtScheduleControlView(&$otSchedule, $showControl = true)
    {
        $this->otSchedule = &$otSchedule;
        $this->showControl = $showControl;
    }
    
    function GetDisplayedSchedule()
    {
        return $this->otSchedule;
    }

    function GenerateHtml()
    {
        $occurenceDate = $this->otSchedule->getDateOfTransaction(); 
        $description = 'on ' . $occurenceDate->toString('M jS, Y');

        return FillScheduleControlViewTemplate($this->otSchedule->getUid(), 'ot', $description, $this->showControl);
    }
}


?>
