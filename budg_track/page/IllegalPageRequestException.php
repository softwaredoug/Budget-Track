<?php

include_once('page/view/IllegalPageErrorView.php');

// Thrown in an attempt was made to access a page that doesn't exist
class IllegalPageRequestException extends Exception
{
    function IllegalPageRequestException()
    {
    }


    function getErrorView()
    {
        return new IllegalPageErrorView();
    }
}


class PageParameterNotFoundException extends IllegalPageRequestException
{
}



?>
