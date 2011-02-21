<?php


include_once('database/DatabaseObject.php');
include_once('PhoneNum.php');

class User extends DatabaseObject
{
    // Its easier just to name these the same as the 
    // column names in the database, if you don't you'll 
    // need to define functions here for converting to/from
    // var and col strings (see the base class for these funcs)
    var $c_uid;
    var $c_username;
    var $c_password;

    var $phoneNums;


    function User()
    {
        $this->c_password = 'default';
        $this->DatabaseObject();
        $this->phoneNums = array();
    }

    function GetUid()
    {
        return $this->c_uid;
    }

    function GetUsername()
    {
        return $this->c_username;
    }

    function GetPassword()
    {
        return $this->c_password;
    }

    function SetUsername($c_username)
    {
        if (is_string($c_username))
        {
            $this->c_username = $c_username;
        }
    }
    
    function SetPassword($c_password)
    {
        if (is_string($c_password))
        {
            $this->c_password = $c_password;
        }
    }

    function AddPhoneNum($phoneNum)
    {
        if (is_object($phoneNum))
        {
            $this->phoneNums[] = $phoneNum;
        }
    }

    function &GetPhoneNums()
    {
        return $this->phoneNums;
    }

    // These are the two functions required to be implemented 
    // in a DatabaseObject. getAsRow serializes the object as
    // a row in the corresponding database table as an associative
    // array of columnname=>value
    //
    // So here we would have
    // $row['c_uid'] = $this->uid;
    // $row['c_username'] = $this->username;
    // $row['c_password'] = $this->password
    //  
    // For setAsRow, a row argument is passed in and the object
    // should assign its members accordingly
    function getAsRow()
    {
        $row = array();
        $row['c_uid'] = $this->c_uid;
        $row['c_username'] = $this->c_username;
        $row['c_password'] = $this->c_password;
        return $row;
    }

    function setFromRow(&$row)
    {
        $this->c_uid = $row['c_uid'];
        $this->c_username = $row['c_username'];
        $this->c_password = $row['c_password'];
    }
    
    // The referrers are tables with a column that referring back to this object 
    // We decided that for the case of phone numbers, this implies ownership
    // of the phone numbers by the User type. The user has setup this relationship
    // via the DatabaseTable. So when the DatabaseTable needs to set or retreived
    // The users owned PhoneNums, it will call getReferrersOfType to gain access to 
    // the referrers
    function &getReferrersOfType($typename)
    {
        if ($typename == 'PhoneNum')
        {
            return $this->GetPhoneNums();
        }
    }


}



?>
