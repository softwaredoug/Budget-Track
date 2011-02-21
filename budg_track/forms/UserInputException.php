<?php

include_once('page/view/UserInputErrorView.php');
include_once('page/view/NoticeView.php');

class UserInputException extends Exception
{
    var $errorNoticeView;

    function UserInputException($fieldName, $message)
    {
        $this->errorNoticeView = new NoticeView( new UserInputErrorView($fieldName, $message));
    }

    function GetErrorNoticeView()
    {
        return $this->errorNoticeView;
    }
}


?>
