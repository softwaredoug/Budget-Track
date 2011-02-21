<?php

class SavedBudget extends DatabaseObject
{
    // values in row
    var $uid;
    var $hashpw;

    // rows that refer to me through index
    var $transactions;

    function SavedBudget()
    {
        $this->DatabaseObject();
        $this->transactions = array();
    }

    function setFromRow($row)
    {
        if (is_array($row))
        {
            $this->uid = intval($row['uid']);
            $this->hashpw = $row['hashpw'];
        }
    }

    function &getAsRow()
    {
        $row = array();
        $row['uid'] = $this->uid;
        $row['hashpw'] = $this->hashpw;
        return $row;
    }

    function GetUid()
    {
        return $this->uid;
    }

    function SetUid($val)
    {
        if (is_int($val))
        {
            $this->uid = $val;
        }
    }

    function GetHashPw()
    {
        return $this->hashpw;
    }

    function SetHashPw($val)
    {
        $this->hashpw = $val;
    }

    function &getTransactions()
    {
        return $this->transactions;
    }

    function &getReferrersOfType($typeName)
    {
        if ($typeName == 'Transaction')
        {
            return $this->getTransactions();
        }
    }
}

?>
