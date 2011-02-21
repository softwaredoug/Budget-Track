<?php

/*Include All Possible Database Objects?*/
include_once('DatabaseObject.php');
include_once("MySqlConnection.php");
include_once("MySqlResult.php");

// Represents a table that refers to another table
// through one of its columns. The referring column
// should represent a unique integer identifier into the
// table it refers to.
class DatabaseTableReferrer
{
    var $dbTable;      /*DatbaseTable*/
    var $referringCol; /*string*/

    function DatabaseTableReferrer(&$dbTable, $referringCol)
    {
        $this->dbTable = $dbTable;
        $this->referringCol = $referringCol;
    }

    function &getDbTable()
    {
        return $this->dbTable;
    }

    function getReferringCol()
    {
        return $this->referringCol;
    }
}


// Represents an SQL tabe with the following constraints:
// - An integer column exists which is unique to each row 
//   (there is a unique identifier)
// - (-inf ...0) inclusive are not legal values for this unique
//   identifier
// - A DatabaseObject exists which can represent a row to
//   the rest of the system.
// The database object must not
// - Change the value of its own unique identifier (the 
//   DatabaseTable will manage it)
class DatabaseTable
{
    var $db;
    var $tableName;
    var $dbObjType; /*string*/
    var $uidCol; /*string*/
    var $referringTables; /*array of objs*/

    function DatabaseTable(&$db, $tableName, $dbObjType, $uidCol)
    {
        if (!is_object($db))
        {
            die("Error in DatabaseTable constructor--Database specified to DatabaseTable object not an object");
        }
        // TODO: Specify column names to ensure operations are correct
        $this->db = $db;
        $this->tableName = $tableName;
        $this->dbObjType = $dbObjType;
        $this->uidCol = $uidCol;
        $this->referringTables = array();
    }
    
    // Add a table which referrs to me
    function AddReferrer(&$dbTable, $column)
    {
        $this->referringTables[] = new DatabaseTableReferrer($dbTable, $column);
    }

    // Retreive the databa
    function getDatabase()
    {
        return $this->db;
    }

    // Retreive the naem of this table
    function getName()
    {
        return $this->tableName;
    }

    // Retreiv the name of the type I store my rows in
    function getDbObjType()
    {
        return $this->dbObjType;
    }

    function getIdCol()
    {
        return $this->uidCol;
    }

    // Retreive the unique id from the object
    function getObjId(&$obj)
    {
        if (is_object($obj))
        {
            $idVar = $obj->convertToVariableFromColumn($this->uidCol);
            if ($idVar)
            {
                $id = $obj->$idVar;
                if (!is_object($id))
                {
                    $id = intval($id);
                    if ($id == 0)
                    {
                        $id = false;
                    }
                    return $id;
                }
            }
        }

        // Likely a serious error    
        return false;
    }

    // Set the object's unique identifier
    function setObjId(&$obj, $id)
    {
        if (is_object($obj))
        {
            $idVar = $obj->convertToVariableFromColumn($this->uidCol);
            if ($idVar && is_int($id))
            {
                $obj->$idVar = $id;
            }
        }
    }

    /**************************************************************************
     * CRUD operations
     **************************************************************************/
    
    // Insert the object into this table if it does not already exist
    // Insert the object's referrers into the table if they do not already exist
    //
    // Returns true if insert succesful, or it seems that the insert already
    // occured.
    function insert(/*DatbasaeObject*/ &$obj) 
    {
        $newUid = 0;
        if (is_object($obj))
        {

            $success = true;
            //if ($this->getObjId($obj) == false) 
            /*hasn't been inserted yet, if it has been, no biggy, move on*/
            {
                $db = $this->getDatabase();
                $table = $this->getName();
                $res = null;
                $row = $obj->getAsRow();


                $cmd =& $db->createCommand();
                $res = $cmd->autoExecute($this->getName(), $row, DB_AUTOQUERY_INSERT);
                $newUid = $res->identity();
                $this->setObjId($obj, $newUid);

            }
            //else
            {
                // Should update?
            }
            // Insert my referers
            // Foreach referring table
            foreach ($this->referringTables AS $key => $dbReferrer)
            {
                $referringDbTable = $dbReferrer->getDbTable();
                // For each instance that obj owns that refers to obj
                foreach ($obj->getReferrersOfType($referringDbTable->getDbObjType()) AS $key => $objsReferrer)
                {
                    // Setup the link between the two tables
                    $row = $objsReferrer->getAsRow();
                    $row[ $dbReferrer->getReferringCol() ] = $newUid;
                    $objsReferrer->setFromRow($row);

                    $success = ($success && $referringDbTable->insert($objsReferrer));
                }
            }
            if (!$success)
            {
                trigger_error('Insert partially failed, integrity of the database may be compromised.', E_USER_ERROR);
                //TODO - cascade delete to maintain my integrity
                // $this->delete($obj)
            }
            return $success;
        }
        return false;
    } //}}}

        
    // Update the changes made to the specified object
    //  Update the changes made to its contained objects.
    //  Return true when update succesful.
    function update(/*DatbaseObject*/ &$obj) 
    {
        // Is Obj my kind of obj
        // Am I dbObjType?
        if (is_object($obj))
        {
            $id = $this->getObjId($obj); 
            if ($id)
            {
                $success = true;
                $db = $this->getDatabase();
                $table = $this->getName();

                $row = $obj->getAsRow();

                // Now use this to update the object's tuple in the database.
                $cmd =& $db->createCommand();
                $cmd->autoExecute($table, $row, DB_AUTOQUERY_UPDATE, "$this->uidCol = $id");
                
                // Update my referers
                // Foreach referring table
                foreach ($this->referringTables AS $key => $dbReferrer)
                {
                    $referringDbTable = $dbReferrer->getDbTable();
                    // For each instance that obj owns that refers to obj in the db
                    foreach ($obj->getReferrersOfType($referringDbTable->getDbObjType()) AS $key => $objsReferrer)
                    {
                        $row = $objsReferrer->getAsRow();
                        print_r($row);
                        if (isset($row[ $dbReferrer->getReferringCol() ]))
                        {
                            // Update preexisting rows
                            $success = ($success && $referringDbTable->update($objsReferrer));
                        }
                        else
                        {
                            // Insert new rows
                            $row = $objsReferrer->getAsRow();
                            $row[ $dbReferrer->getReferringCol() ] = $id;
                            $objsReferrer->setFromRow($row);

                            print_r($objsReferrer);
    
                            $success = ($success && $referringDbTable->insert($objsReferrer));
                        }
                    }
                }
                if (!$success)
                {
                    trigger_error('Update partially failed, integrity of the database may be compromised.', E_USER_ERROR);
                    // TODO -cascade delete? nothing?
                }
                return $success;
            }
        }
        return false;
    } //}}}

    // Selects with the unique id
    function &selectUsingId($uid)
    {
        if (is_int($uid))
        {
            $res = $this->selectWhere("$this->uidCol = $uid");
            return $res[0];
        }
    }


    // Produce a set of DatabaseObjects based on the given where clause.
    function &selectWhere($where = '')
    {
        $db = $this->getDatabase();
        $table = $this->getName();
        $obj = array();

        if ($where != '')
        {
            $where = 'where ' . $where;
        }

        // TODO - How can I make this smarter to do 1 query for me and all my referrers?
        $result =& $db->query($q = "select * from `$table` $where");

        if ($result->numRows() > 0)
        {
            $i = 0;
            while ($row = $result->fetchRow())
            {
                // An error here means the user code does not have dbObjType defined
                // Make sure that the type specified to hold the database data in the 
                // table's constructor is defined when you invoke the table's code
                $obj[$i] = new $this->dbObjType();
                $obj[$i]->setFromRow($row);
                $id = $this->getObjId($obj[$i]);


                // If no Id after select... is bad
                if ($id)
                {
                    // Select obj i's referrers
                    foreach ($this->referringTables AS $key => $dbReferrer)
                    {
                        $referringDbTable = $dbReferrer->getDbTable();
                        $referringType = $referringDbTable->getDbObjType();
                        $referringDbCol  = $dbReferrer->getReferringCol();
                        
                        $newReferringObjs = $referringDbTable->selectWhere("$referringDbCol = $id");
                        
                        
                        // Attach each to this object
                        $referrers = &$obj[$i]->getReferrersOfType($referringType, $referringObj);
                        $referrers = $newReferringObjs;
                    }
                        
                }
                ++$i;
            }
        }
        return $obj;
    }

    // Refresh the referrers to this object
    function selectReferrers(&$obj)
    {
        $id = $this->getObjId($obj);
        if ($id)
        {
            // Select obj's referrers
            foreach ($this->referringTables AS $key => $dbReferrer)
            {
                $referringDbTable = $dbReferrer->getDbTable();
                $referringType = $referringDbTable->getDbObjType();
                $referringDbCol  = $dbReferrer->getReferringCol();
                $newReferringObjs = $referringDbTable->select("$referringDbCol = $id");

                
                // Attach each to this object
                $referrers = &$obj->getReferrersOfType($referringType, $referringObj);
                $referrers = $newReferringObjs;
            }

        }
    }

    // Attempt to delete the object
    function delete(/*DatbaseObject*/ &$obj)
    {
        // Is Obj my kind of obj
        // Am I dbObjType?
        if (is_object($obj))
        {
            $db = $this->getDatabase();
            $table = $this->getName();

            $id = $this->getObjId($obj);

            if ($id)
            {
                // TODO - a sigle delete query
                $result =& $db->query("delete from `$table` where $this->uidCol = $id");
                // Delete my referrers
                foreach ($this->referringTables AS $key => $dbReferrer)
                {
                    $referringDbTable = $dbReferrer->getDbTable();
                    $referringTableName =  $referringDbTable->getName();
                    $referringDbCol   = $dbReferrer->getReferringCol();
                    $referringDbId    = $referringDbTable->getIdCol();
                    $result =& $db->query("delete from `$referringTableName` where $referringDbCol = $id");
                    
                    foreach ($obj->getReferrersOfType($referringDbTable->getDbObjType()) AS $key => $objsReferrer)
                    {
                        // Unset this objects unique id;
                        unset($objsReferrer->$referringDbId);
                    }

                }
                return true;
            }
            else
            {
                return false;
            }
        }
    }


}



?>
