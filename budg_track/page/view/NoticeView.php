<?php

include_once('page/view/HtmlView.php');
include_once('php_code_library/utility/Utility.php');
include_once('php_code_library/template/Template.php');

class NoticeView extends HtmlView
{
    var $noticeToGive;

    function NoticeView(HtmlView $noticeToGive)
    {
        $this->noticeToGive = $noticeToGive;
    }

    function generateHtml()
    {
        $noticeToGiveHtml = $this->noticeToGive->generateHtml();
        static $noticeTemplate = '';
        if ($noticeTemplate == '')
        {
            $noticeTemplate = Utility::getFile('template/view/noticeView.html');
        }

        // Fill in the notice with the given html view
        return Template::replaceValues($noticeTemplate, array('NOTICE_TO_GIVE' => $noticeToGiveHtml));
    }
}



?>
