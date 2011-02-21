<?php

include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');

class IllegalPageErrorView extends HtmlView
{

    function IllegalPageErrorView()
    {
    }

    function generateHtml()
    {
        static $errorTemplate = '';
        if ($errorTemplate == '')
        {
            $errorTemplate = Utility::getFile('template/view/illegalPageErrorView.html');
        }
        return $errorTemplate;
    }

    
}



?>
