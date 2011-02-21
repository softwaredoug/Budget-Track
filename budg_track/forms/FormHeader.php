<?php

include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');

class IllegalFormHeader extends Exception
{
}


class FormHeader extends HtmlView
{
    var $method;
    var $action;

    function FormHeader($method, $action)
    {
        static $actionRegex = 'index\.php\?page=([a-z]|-|_)+((&.+)*)*';
        if (ereg($actionRegex, $action) && 
           ($method == 'post' || $method == 'get') )
        {
            $this->action = $action;
            $this->method = $method;
        }
        else
        {
            throw new IllegalFormHeader();
        }
    }

    function generateHtml()
    {
        static $formHeaderHtml = '';
        if ($formHeaderHtml == '')
        {
            $formHeaderHtml = Utility::getFile('template/view/formHeader.html');
        }
        return Template::replaceValues($formHeaderHtml, array('METHOD' => $this->method, 'ACTION' => $this->action));
    }
}
