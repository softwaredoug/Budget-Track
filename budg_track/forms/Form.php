<?php


include_once('page/view/HtmlView.php');
include_once('forms/FormHeader.php');

class Form extends HtmlView
{
    var $formHeader;

    function Form($method, $action)
    {
        $this->formHeader = new FormHeader($method, $action);
    }

    function generateHtml()
    {
    }
    
    function didUserSubmitInput($userSentData)
    {
    }
}


?>
