<?php

include_once('classes/MySqlDatabaseTable.php');

/* Application specific 
 */
class BudgetTrackerRow extends MySqlDatabaseRow
{
    function BudgetTrackerObject($dbRow)
    {
        $this->dbRow = $dbRow;
    }

    function getUid()
    {
        return $this->dbRow->getValue('uid');
    }
}

class TableReference
{
    var $table;
    var $referringColumn;

    function getTable()
    {
        return $this->table;
    }

    function getReferringColumn()
    {
        return $this->referringColumn;
    }

    function TableReference($table, $referringColumn)
    {
        if (is_object($table) && is_string($referringColumn)) 
        {
            $this->table = $table;
            $this->referringColumn = $referringColumn;
        }
    }
}


/*  Manages a specific kind of budget tracker object
 *
 */
class BudgetTrackerTable extends MySqlDatabaseTable
{
    var $referencesToMe;
    var $mySqlTable;

    function BudgetTrackerTable($table, $tableLayout)
    {
        global $obj;

        /*specify table structure*/
        $this->MySqlDatabaseTable($obj['db'], $table, $tableLayout); 
        $this->referencesToMe = array();
    }

    /* Point out a related table to me and the param I need to use to access
     * the row in the table related to rows in my table.
     */
    function addReferenceToMe($table, $column)
    {
        if (is_object($table) && is_string($column))
        {
            $this->referencesToMe[] = new TableReference($table, $column);
        }
        else
        {
            trigger_error('Specify a table name and relationship to this table when cascading');
        }
    }

    /*
     */
    function deleteRowAndReferences($uid)
    {
        # Combine into 1 query
        foreach ($this->referencesToMe AS $tableReference)
        {
            $tableObj = $tableReference->getTable();
            $tableObj->delete( array( $tableReference->getReferringColumn() => $uid));
        }
        $this->delete(array('uid'=>$uid));
    }


    /**/
    function selectRowAndReferences($uid)
    {
        # Combine into 1 query
        # TODO
       /* $retValue = array();
        $retValue = $this->select(array('uid'=>$uid));
        foreach ($this->referencesToMe AS $tableReference)
        {
            $tableObj = $tableReference->getTable();
            $rows = $tableObj->select( array( $tableReference->getReferringColumn() => $uid));
        }*/
    }

}

?>
