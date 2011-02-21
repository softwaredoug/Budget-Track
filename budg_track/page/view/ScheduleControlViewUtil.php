<?php

include_once('php_code_library/template/Template.php');
include_once('php_code_library/utility/Utility.php');

function FillScheduleControlViewTemplate($uid, $type, $description, $showControl)
{
    if (is_int($uid) && is_string($type) && is_string($description))
    {
        #Only load the file once,
        static $schedControlViewTemplate = '';
        $schedHtml = '';
        if ($schedControlViewTemplate == '')
        {
            $schedControlViewTemplate = Utility::getFile('template/view/scheduleControlView.html');
        }

        if ($showControl)
        {
            $schedHtml = Template::unhideBlock($schedControlViewTemplate, 'CONTROLS');
        }
        else
        {
            $schedHtml = $schedControlViewTemplate;
        }

        $rVal = Template::replaceValues($schedHtml, array('UID' => $uid, 'TYPE'=>$type, 'DESCRIPTION'=>$description));
        return trim($rVal);

    }
    else
    {
        echo "Error!?";
    }
}


// Assumes the viewType is constructed with a single obj type
function SchedArrayToSequentiallyDisplayedView($arrayOfObjs, $viewType, $showControl = true, $delimiters = false)
{
    $seqView = new SequentiallyDisplayedView();
    if (is_array($delimiters))
    {
        $seqView->SetDelimiters($delimiters);
    }
    foreach ($arrayOfObjs AS $key => &$obj)
    {
        $view = new $viewType($obj, $showControl);
        $seqView->AppendView($view);
    }
    return $seqView;
}


?>
