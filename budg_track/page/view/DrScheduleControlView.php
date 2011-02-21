<?php
# Display the control view of a "Days Repeated Since Last Occurencence" (abreviated dr) 
# as in <X> <E> (SCHED_TEXT)
#

include_once('page/view/ScheduleControlViewUtil.php');
include_once('classes/DaysRepeatedAfterFirstOccurenceSchedule.php');
include_once('php_code_library/datetime/DateAndTime.php');

class DrScheduleControlView extends HtmlView
{
    var $drSchedule;
    var $showControl;

    function DrScheduleControlView(&$drSchedule, $showControl = true)
    {
        $this->showControl = $showControl;
        $this->drSchedule = &$drSchedule;
    }
    
    function GetDisplayedSchedule()
    {
        return $this->drSchedule;
    }

    function GenerateHtml()
    {
        $startDateStr = $this->drSchedule->getStartDate();
        $startDateStr = $startDateStr->toString('F dS, Y');
        $description = 'on ' . $startDateStr . '--repeating every ' . $this->drSchedule->getDaysAfterToRepeat() . ' days after';
        return FillScheduleControlViewTemplate(intval($this->drSchedule->getUid()), 'dr', $description, $this->showControl);
    }
}


?>
