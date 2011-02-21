<?php

include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');

class UserInputErrorView extends HtmlView
{
    var $fieldName;
    var $errorMessage;

    function UserInputErrorView($fieldName, $errorMessage)
    {
        $this->fieldName = htmlspecialchars($fieldName);
        $this->errorMessage = htmlspecialchars($errorMessage);
    }

    function generateHtml()
    {
        static $errorTemplate = '';
        if ($errorTemplate == '')
        {
            $errorTemplate = Utility::getFile('template/view/userInputErrorView.html');
        }
        return Template::replaceValues($errorTemplate, array('FIELD_NAME' => $this->fieldName,
                                                             'ERROR_MESSAGE' => $this->errorMessage));

    }
}

?>
