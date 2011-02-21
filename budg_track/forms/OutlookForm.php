<?php

include_once('page/view/HtmlView.php');
include_once('forms/FormHeader.php');
include_once('forms/validators/UserInputOutlookParams.php');
include_once('php_code_library/datetime/DateAndTime.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');


class OutlookForm extends HtmlView
{
    var $formHeader;

    var $displayedStartDate;
    var $displayedStartBalance;
    var $displayedNumDays;

    function OutlookForm($method, $action)
    {
        $this->displayedStartDate = null;
        $this->displayedStartBalance = 'unset number';
        $this->displayedNumDays = 'unset number';
        $this->formHeader = new FormHeader($method, $action);
    }

    function generateHtml()
    {
        static $outlookFormTemplate = '';
        if ($outlookFormTemplate == '')
        {
            $outlookFormTemplate = Utility::getFile('template/view/outlookForm.html');
        }
        $html = Template::replaceValues($outlookFormTemplate, 
                            array('FORM_HEADER' => $this->formHeader->generateHtml()));

        if ($this->displayedStartDate != null)
        {
            $html = Template::replaceValues($html, 
                            array('START_DATE' => $this->displayedStartDate->toString('Y-m-d')));
        }
        if ("$this->displayedStartBalance" != 'unset number')
        {
            $html = Template::replaceValues($html, 
                            array('START_BALANCE' => $this->displayedStartBalance));
        }
        if ("$this->displayedNumDays" != 'unset number')
        {
            $html = Template::replaceValues($html, 
                            array('NUM_DAYS' => $this->displayedNumDays));
        }
        return $html;
    }

    function SetDisplayedStartDate( DateAndTime $startDate )
    {
        $this->displayedStartDate = $startDate;
    }

    function SetDisplayedStartBalance( $startBalance)
    {
        if (is_numeric($startBalance))
        {
            $this->displayedStartBalance = $startBalance;
        }
    }

    function SetDisplayedNumDays( $numDays )
    {
        if (is_int($numDays))
        {
            $this->displayedNumDays = $numDays;
        }
    }

    function didUserSubmitInput($userSentData)
    {
        return isset($userSentData['submit_button']);
    }
    
    function ExtractUserInputOutlookParams($userSentData)
    {
        return new UserInputOutlookParams($userSentData['start_date'], 
                                          $userSentData['num_days'], 
                                          $userSentData['start_balance']); 
    }
}
