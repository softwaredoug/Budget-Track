<?php


include_once('page/view/HtmlView.php');

// A little Decorator pattern going on here
class SequentiallyDisplayedView extends HtmlView
{
    var $views;
    var $nextInsertKey;
    var $delimiters;

    function SequentiallyDisplayedView()
    {
        $this->views = array();
        $this->nextInsertKey = 0;
        $this->delimiters = 0;
    }

    function AppendView(HtmlView $view)
    {
        $this->views[$this->nextInsertKey] = $view;
        $this->nextInsertKey++;
    }

    // Delimiters should be a 0 based array
    // with the 0th element corresponding to what
    // is to be placed between the 0th and 1st element
    // and so on 
    function SetDelimiters(array $delimiters)
    {
        if (is_array($delimiters))
        {
            $this->delimiters = $delimiters;
        }
    }

    function GenerateHtml()
    {
        $retVal = '';
        $currViewIdx = 0;
        # append each view to each other in the order which they were added
        foreach($this->views as $view)
        {
            $retVal .= $view->GenerateHtml();
            if (is_array($this->delimiters))
            {
                if (isset($this->delimiters[$currViewIdx]))
                {
                    $retVal .= $this->delimiters[$currViewIdx];
                }
            }
            $currViewIdx++;
        }
        return $retVal;
    }
}


// Assumes the viewType is constructed with a single obj type
function ObjArrayToSequentiallyDisplayedView($arrayOfObjs, $viewType, $delimiters = false)
{
    $seqView = new SequentiallyDisplayedView();
    if (is_array($delimiters))
    {
        $seqView->SetDelimiters($delimiters);
    }
    foreach ($arrayOfObjs AS $obj)
    {
        $view = new $viewType($obj);
        $seqView->AppendView($view);
    }
    return $seqView;
}



?>
