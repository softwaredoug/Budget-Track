
// Fetch the outlook from the site 
function recalcWithout(occurenceId)
{
    indexOfInIgnoredTransactionOccurences = this.algorithmParams.ignoredTransactionOccurences.indexOf(occurenceId);
    if (indexOfInIgnoredTransactionOccurences == -1)
    {
        this.algorithmParams.ignoredTransactionOccurences.push(occurenceId);
    }
    else
    {
        delete this.algorithmParams.ignoredTransactionOccurences[indexOfInIgnoredTransactionOccurences];
    }
    this.drawOutlook();
}

// Fetch the outlook from the site 
function fetchOutlook()
{
    function bindOutlookCallbackToObj(obj, method)
    {
        return function (jsonResults)
        {
            method.apply(obj, [jsonResults]);
        };
    }

    callback = bindOutlookCallbackToObj(this, this.onOutlookReceived);
    $.getJSON('index.php?fetch=outlook&budgetId=' + this.algorithmParams.budgetId + 
              '&start_date=' + this.algorithmParams.startDate + 
              '&num_days=' + this.algorithmParams.numDays + 
              '&starting_balance=' + this.algorithmParams.startBalance, 
              callback);
}

// Create a row in the outlook table containing the specified content
function createOutlookRow(id, transactionScheduledOccurenceDateStr, transactionNameStr, transactionDollarAmountStr, resultingBalanceStr)
{
    var newRow = document.createElement('tr');

    var dateText = document.createTextNode(transactionScheduledOccurenceDateStr)
    var dateCell = document.createElement('td');
    dateCell.appendChild(dateText);
    var nameText = document.createTextNode(transactionNameStr)
    var nameCell = document.createElement('td'); 
    nameCell.appendChild(nameText);
    var dollarAmountText = document.createTextNode(transactionDollarAmountStr)
    var dollarAmountCell = document.createElement('td');
    dollarAmountCell.appendChild(dollarAmountText);
    var resultingBalanceText = document.createTextNode(resultingBalanceStr)
    var resultingBalanceCell = document.createElement('td');
    resultingBalanceCell.appendChild(resultingBalanceText);
    
    function bindEventCallbackToObj(obj, method, id)
    {
        return function ()
        {
            method.apply(obj, [id]);
        };
    }

    newRow.onclick = bindEventCallbackToObj(this, this.recalcWithout, id); 
    newRow.setAttribute('id', "transactionOccurence" + id);
    
    newRow.appendChild(dateCell);
    newRow.appendChild(nameCell);
    newRow.appendChild(dollarAmountCell);
    newRow.appendChild(resultingBalanceCell);
    return newRow;

}

// Fill in table here with appropriate
function onOutlookReceived(scheduledTransactionOccurences)
{
    this.algorithmResults.scheduledTransactionOccurences = scheduledTransactionOccurences;
    this.drawOutlook();
}

function removeAllChildren(parentElement)
{
    // code taken from
    // https://developer.mozilla.org/En/DOM/Node.removeChild
    while (parentElement.firstChild)
    {
        parentElement.removeChild(parentElement.firstChild);
    }
}

function drawOutlook()
{
    var outlookBody = document.getElementById('outlookbody');
    removeAllChildren(outlookBody);
    

    var balance = this.algorithmParams.startBalance;
    scheduledTransactionOccurences = this.algorithmResults.scheduledTransactionOccurences;
    for (occurenceId in scheduledTransactionOccurences)    
    {
        var ignored = (this.algorithmParams.ignoredTransactionOccurences.indexOf(occurenceId) > -1);
        if (!ignored)
        {
            balance += parseInt(scheduledTransactionOccurences[occurenceId].dollar_amount);
        }
        scheduledTransactionOccurences[occurenceId].resulting_balance = balance; 
        var row = this.createOutlookRow(occurenceId,
                                   scheduledTransactionOccurences[occurenceId].date, 
                                   scheduledTransactionOccurences[occurenceId].name, 
                                   scheduledTransactionOccurences[occurenceId].dollar_amount,
                                   scheduledTransactionOccurences[occurenceId].resulting_balance.toFixed(2));
        if (ignored)
        {
            row.setAttribute('class', 'disabledOutlookOccurence');
        }
        outlookBody.appendChild(row);
    }
        
}

function Outlook(budgetId, startDate, numDays, startBalance, outlookTableBodyId)
{
    this.algorithmParams = {};
    this.algorithmParams.budgetId = budgetId;
    this.algorithmParams.ignoredTransactionOccurences= [];
    this.algorithmParams.startDate = startDate; 
    this.algorithmParams.numDays = numDays;
    this.algorithmParams.startBalance = startBalance;

    this.algorithmResults = {};
    this.algorithmResults.scheduledTransactionOccurences = [];

    this.pageEntities = {};
    this.pageEntities.tBody = outlookTableBodyId;

    // fill in methods
    this.recalcWithout = recalcWithout;
    this.fetchOutlook= fetchOutlook;
    this.onOutlookReceived = onOutlookReceived;
    this.drawOutlook = drawOutlook;
    this.createOutlookRow = createOutlookRow;
}


// to-do: define a callback that calls fetch outlook then formats nicely into the table
// add ability to recalculate the table on the client side when one of the entries is clicked
//  -> need to learn how to emulate a dictionary in javascript

function initializeOutlook(budgetId, startDate, numDays, startBalance, outlookTableBodyId)
{
    outlook = new Outlook(budgetId, startDate, numDays, startBalance, outlookTableBodyId);
    outlook.fetchOutlook();
}
