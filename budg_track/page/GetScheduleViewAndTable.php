<?php

include_once('page/view/DrScheduleControlView.php');
include_once('page/view/OtScheduleControlView.php');
include_once('page/view/MsScheduleControlView.php');


function GetScheduleViewAndTable($type, $uid, &$dbTable, $showControl = true)
{
    if (is_numeric($uid) || is_int($uid))
    {
        $uid = intval($uid);
        global $obj;
        $viewType = '';
        if ($type == 'ms')
        {
            $dbTable = $obj['tables']['monthlySchedule'];
            $viewType = 'MsScheduleControlView'; 
        }
        else if ($type == 'ot')
        {
            $dbTable = $obj['tables']['oneTimeSchedule'];
            $viewType = 'OtScheduleControlView'; 
        }
        else if ($type == 'dr')
        {
            $dbTable = $obj['tables']['daysRepeatedAfterFirstOccurenceSchedule'];
            $viewType = 'DrScheduleControlView'; 
        }
        if ($viewType)
        {
            $scheduleObject = $dbTable->selectUsingId($uid);
            if ($scheduleObject)
            {
                $view = new $viewType($scheduleObject, $showControl);
                return $view;
            }
            else
            {
                return false;
            }
        }
    }
    throw new IllegalPageRequestException;
}


?>
