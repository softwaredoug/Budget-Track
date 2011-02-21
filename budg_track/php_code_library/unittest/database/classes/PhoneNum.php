<?php

include_once('database/DatabaseObject.php');


class PhoneNum extends DatabaseObject
{
    var $c_uid;
    var $c_user;
    var $c_phonenum;

    function PhoneNum()
    {
        $this->DatabaseObject();
    }

    function GetUid()
    {
        return $this->c_uid;
    }

    function GetUserId()
    {
        return $this->c_user;
    }

    function GetPhoneNum()
    {
        return $this->c_phonenum;
    }

    function SetPhoneNum($phoneNum)
    {
        if (is_int($phoneNum))
        {
            $this->c_phonenum = $phoneNum;
        }
    }
    
    // These are the two functions required to be implemented 
    // in a DatabaseObject. getAsRow serializes the object as
    // a row in the corresponding database table as an associative
    // array of columnname=>value
    //
    // For setFromRow, a row argument is passed in and the object
    // should assign its members accordingly
    function getAsRow()
    {
        $row = array();
        $row['c_uid'] = $this->c_uid;
        $row['c_user'] = $this->c_user;
        $row['c_phonenum'] = $this->c_phonenum;
        return $row;
    }

    function setFromRow(&$row)
    {
        $this->c_uid = $row['c_uid'];
        $this->c_phonenum = $row['c_phonenum'];
        $this->c_user = $row['c_user'];
    }

}



?>
