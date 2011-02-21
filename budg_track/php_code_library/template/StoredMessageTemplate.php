<?php
/*Extends the standalone template, adding database stored text. Right now database is specefic to OASC*/

include_once("Template.php");

class StoredMessageTemplate extends Template
{ 

    /* Accepts a string and an array of characters in the format {TEMLATE_ID_1 \> STORED_IDENTIFIER1, TEMPLATEDID2 => STORED_IDENTIFIER2...}
     * and queries the t_message for the text associated with each identifier. Then performs
     * replaces each instance of the text {MESSAGE_STORED_IDENTIFIER1}, {TEMPLATEID2}, ...
     * with the text associated with identifier in the database.
     */
    function replaceValuesWithStoredMessage($data, $storedIdentifiers)
    {
        global $obj;
        if (is_array($storedIdentifiers))
        {
            $storedIdsForSql = preg_replace("/(\w+)/e", "\"'\" . strtolower($1) . \"'\"", $storedIdentifiers); 
           
            // Load stored identifiers 
            $query = "SELECT * FROM t_message WHERE c_identifier IN (" . implode(", ", $storedIdsForSql) .")";
            $result =& $obj['db']->query($query);
            
            // Convert TEMPLATEIDS => STOREDIDS to
            //           STOREDIDS => TEMPLATEIDS
            $storedIdentifiers = array_flip($storedIdentifiers);

            while ($row =& $result->fetchRow()) 
            {
                if (is_string(($storedIdentifiers[ $row['c_identifier'] ])))
                {
                    $values[ strtoupper($storedIdentifiers[ $row['c_identifier'] ]) ] = $row['c_text'];
                }
            }
            $data = Template::replaceValues($data, $values);
            return $data;
        }
        else
        {
            trigger_error("Parameter '\$storedIdentifiers' is not an array", E_USER_ERROR);
        }
    }
}

?>
