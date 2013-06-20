<?php


/*Globals*/

set_include_path('.:./php_code_library');

$page_query = array(); /*Parameters of the current html query, including useful get and post vars*/
$page_result = array();     /*The resulting html page*/
$obj = array();        /*Global objects*/

include_once('utility/Utility.php');
include_once('php_code_library/template/Template.php');
include_once('database/MySqlConnection.php');
include_once('database/DatabaseTable.php');

include_once('classes/SavedBudget.php');
include_once('classes/Transaction.php');
include_once('classes/DaysRepeatedAfterFirstOccurenceSchedule.php');
include_once('classes/MonthlySchedule.php');

include_once('page/IllegalPageRequestException.php');
include_once('dbconfig.php');


$obj['db'] = createDbConnection();
$obj['db']->open();




// Create and link db tables together
$obj['tables']['transaction'] = new DatabaseTable($obj['db'],         // DB
                                                        'transaction',      // SQL Table Name
                                                        'Transaction',      // Corresponding Php Object Name
                                                        'uid');             // SQL Primary key 

$obj['tables']['daysRepeatedAfterFirstOccurenceSchedule'] = new DatabaseTable($obj['db'],
                                                                                  'daysRepeatedAfterFirstOccurenceSchedule',
                                                                                  'DaysRepeatedAfterFirstOccurenceSchedule',
                                                                                  'uid');

$obj['tables']['monthlySchedule'] = new DatabaseTable($obj['db'],
                                                        'monthlySchedule',
                                                        'MonthlySchedule',
                                                        'uid');

$obj['tables']['oneTimeSchedule'] = new DatabaseTable($obj['db'],
                                                        'oneTimeSchedule',
                                                        'OneTimeSchedule',
                                                        'uid');

$obj['tables']['savedBudget'] = new DatabaseTable($obj['db'],
                                                  'savedBudget',
                                                  'SavedBudget',
                                                  'uid');


// entries in these tables refer back to transaction rows, OR transaction rows own entries in those tables
$obj['tables']['transaction']->addReferrer($obj['tables']['daysRepeatedAfterFirstOccurenceSchedule'], 'transaction_uid');
$obj['tables']['transaction']->addReferrer($obj['tables']['monthlySchedule'], 'transaction_uid');
$obj['tables']['transaction']->addReferrer($obj['tables']['oneTimeSchedule'], 'transaction_uid');

// Saved budget 'has a' transaction, OR in transaction budget_id points back to my "owning" budget row
$obj['tables']['savedBudget']->addReferrer($obj['tables']['transaction'], 'budget_id');



// Steup Page Query For Everyones Use
$page_query['fetch'] = ($_GET['fetch']);        // When fetch is set, expect the fetched data to be echo'd directly in json
$page_query['page'] = ($_GET['page']);          // when page is set, page_result will be used to fill in the default template
if ($page_query['fetch'] != '' && $page_query['page'] != '')
{
    die("Page and fetch at the same time?");
}
if ($page_query['page'] == '' && $page_query['fetch'] == '')
{
    // index.php redirects to main page
    $page_query['page'] = 'transaction';
}



// check for and sanitize html post vars input
foreach (array_keys($_POST) as $key)
{
    $page_query['post'][$key] = Utility::postVal($key);
}

// check for and sanitize html get vars input
foreach (array_keys($_GET) as $key)
{
    $page_query['get'][$key] = Utility::getVal($key);
}

$budgetId = 0;
$budgetId = intval($page_query['get']['budgetId']);
if ($budgetId != 0)
{
    $obj['budget'] = $obj['tables']['savedBudget']->selectUsingId($budgetId);
}
// Otherwise generate a budget for this user
else
{
    $obj['budget'] = new SavedBudget();
    $obj['budget']->setHashPw("");
    $obj['tables']['savedBudget']->insert($obj['budget']);

    Utility::redirect('index.php?page=transaction&budgetId=' . intval($obj['budget']->getUid())); 
}


$page_result['body'] = "";
$page_result['title'] = "";
$page_result['meta'] = "";
$page_result['keywords'] = "";
$page_result['final'] = "";
$page_result['active_tab'] = "";


if ($page_query['page'] != '')
{
    try
    {
        // run page that will fill in page_result
        include_once("page/" . $page_query['page']. ".php" );
    }
    catch (IllegalPageRequestException $error)
    {
        $page_result['body'] = $error->getErrorView()->generateHtml();
    }


    // Produce Page Result
    if ($page_result['title'] == '')
    {
        $page_result['title'] = 'Budget Planning Tool';
    }
    if ($page_result['body'] == '')
    {
        $page_result['body'] = 'An error appears to have occured creating this page...';
    }

    // Load into the global page template
    $page_result['final'] = Utility::getFile("template/default.html");

    // Load in the page result params
    $page_result['final'] = Template::replaceValues(($page_result['final']), $page_result );

    //
    if (($page_result['active_tab'] == 'SELECT_TRANSACTION_OVERVIEW' ) ||
       ($page_result['active_tab'] == 'SELECT_OUTLOOK' )) 
    {
        $page_result['final'] = Template::unhideBlock($page_result['final'], 'SHOW_TABS');
        $page_result['final'] = Template::unhideBlock($page_result['final'], $page_result['active_tab']);
    }

    // Fill in magic values that are auto-filled
    $page_result['final'] = Template::replaceValues($page_result['final'], array('MAGIC-BUDGET_UID' => $budgetId));
    $page_result['final'] = Template::finalize($page_result['final'] );

    echo $page_result['final'];
}
else if ($page_query['fetch'] != '')
{
    include_once('fetch/' . $page_query['fetch'] . ".php");
}


?>
