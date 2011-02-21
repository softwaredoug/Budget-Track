<?php


// Abstract base class for database objects managed by
// DatabaseTable
//
// Knows how to initialize itself from a database row and 
// convert itself to  adatabase row with the functions below
class DatabaseObject
{

    function DatabaseObject()
    {
    }

    // Given a row, initializes this object
    // A row is an array keyed by column
    //   column1 => value1,
    //   column2 => value2,
    //   ..
    function setFromRow($row)
    {
    }

    // Convert myself into a row, and return the value
    // Only returns values that have been actually set
    // (unset values are ommited)
    function getAsRow()
    {
    }

    // Given one of my class variables, convert it to the
    // name of the column in the table it is stored.
    // Return false if conversion illegal
    function convertToColumnFromVariable($str)
    {
        return $str;
    }

    // Given a database column of. convert it to the
    // name of the member variable it is stored.
    function convertToVariableFromColumn($str)
    {
        return $str;
    }

    // Cascading functionality - get database objects that 
    // Refer to me so when I am deleted, they will be 
    // deleted. If I am updated/inserted, they will also be 
    // updated/inserted. If I am selected, they will also be
    // selected and passed to me to store (via add Referrer Of Type).
    //   - Typically databases model "containment" by the things
    //     being contained referring back to the containing row,
    //     whereas in typical OO design, the contained objects 
    //     exist within the object. This routine allows us to
    //     go between the two models of containment.
    function &getReferrersOfType($typeName)
    {
        // returns array of $typename objects
    }

    //TODO - instead of DatabaseTable directly messing with teh id,
    // create an interface


    // Return an array of database objects indexed on the specified
    // column.
    //
    // If the column is not unique for each object, the first encountered
    // object is chosen. If the column is undefined, it is not placed in the 
    // returned array
    /*static*/ function &ReIndexObjArrayOnColumn(&$objArray, $column)
    {
        $objArrayReindexed = array();
        foreach ($objArray AS $obj)
        {
            $row = $obj->getAsRow();
            if ($row[$column] && !$objArrayReindexed[$row[$column]]) 
            {
                $objArrayReindexed[$row[$column]] = $obj;
            }
        }
        return $objArrayReindexed;
    }
}



?>
