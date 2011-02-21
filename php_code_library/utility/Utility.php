<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: Utility.php,v 1.14 2008/02/03 17:45:13 outdoy1w Exp $
 *
 * Purpose:  A utility class to handle many common tasks in the system.
 */

class Utility{

    /* {{{getval
     * Get a sanitized version of a $_GET variable from a browser.  If your
     * variables are not sanitized, then the server may be using
     * magic_quotes_gpc.  The .htaccess file should turn this off, but if it's
     * not then just use the alternate code below.  This also applies to postval
     * and cookieval.
     */
    function getval ($key) {
        if (isset($_GET[$key])) {
            if (is_array($_GET[$key])) {
                // build a new array and return it, sanitized
                $toReturn = array();
                while (list($key, $val) = each($_GET[$key])) {
                    $toReturn[$key] = stripslashes($val);
                }
                return $toReturn;
            }
            return stripslashes($_GET[$key]); 
        }
    } //}}}

    /* {{{postval
     * Return a sanitized version of a browser's $_POST variable.
     */
    function postval ($key) {
        if (isset($_POST[$key])) {
            if (is_array($_POST[$key])) {
                // build a new array and return it, sanitized
                $temp = $_POST[$key];
                $toReturn = array();
                while (list($key, $val) = each($temp)) {
                    $toReturn[$key] = stripslashes($val);
                }
                return $toReturn;
            }
            return stripslashes($_POST[$key]); 
        }
    } //}}}
    
    /* {{{cookieval
     * Return a sanitized version of a browser's $_COOKIE variable.
     */
    function cookieval ($key) {
        if (isset($_COOKIE[$key])) {
            if (is_array($_COOKIE[$key])) {
                // build a new array and return it, sanitized
                $temp = $_COOKIE[$key];
                $toReturn = array();
                while (list($key, $val) = each($temp)) {
                    $toReturn[$key] = stripslashes($val);
                }
                return $toReturn;
            }
            return stripslashes($_COOKIE[$key]); 
        }
    } //}}}
    
    /* {{{initialize
     * Do some Magic Initialization as needed.  This sets up things that the
     * rest of the site will need to run correctly.
     */
    function initialize() {
        global $obj;
        global $cfg;

        // Set up the flags
        $result =& $obj['db']->query("select c_title, c_bitmask from t_flag");
        while ($row =& $result->fetchRow()) {
            $cfg['flag'][$row['c_title']] = intval($row['c_bitmask']);
        }

        // Set up the perms
        $result =& $obj['db']->query("select c_title, c_bitmask from t_unixperm");
        while ($row =& $result->fetchRow()) {
            $cfg['perm'][$row['c_title']] = intval($row['c_bitmask']);
        }

        // Set up the status codes
        $result =& $obj['db']->query("select c_title, c_uid from t_status order by c_uid");
        while ($row =& $result->fetchRow()) {
            $cfg['status_id'][$row['c_title']] = $row['c_uid'];
        }

        // Set up the action codes and labels, and list of actions that require
        // an object
        $result =& $obj['db']->query("select c_title, c_uid, c_flags, c_summary from t_action order by c_uid");
        while ($row =& $result->fetchRow()) {
            $cfg['action_id'][$row['c_title']] = $row['c_uid'];
            $cfg['action_summary'][$row['c_uid']] = $row['c_summary'];
            if (intval($row['c_flags']) & $cfg['flag']['applies_to_object']) {
                $cfg['require_object_actions'][] = $row['c_uid'];
            }
        }

        // Set up the flipped action codes
        $cfg['action_title'] = array_flip($cfg['action_id']);

        // Set up the group codes
        $result =& $obj['db']->query("select c_title, c_uid from t_group order by c_uid");
        while ($row =& $result->fetchRow()) {
            $cfg['group_id'][$row['c_title']] = $row['c_uid'];
        }

    } //}}}

    /* {{{checkType
     * Check that the given variable is of the desired type.
     */
    function checkType($type, &$obj, $line, $file, $subtype = 0) {
        $bad = false;
        // Check if $obj is of type $type (or a subclass, if $subtype is set)
        if ($subtype) { 
            // This would be a heck of a lot easier if is_a were supported in
            // older versions of PHP!
            if (!is_subclass_of($obj, $type) && strcasecmp($type, get_class($obj)) != 0) {
                $bad = true;
            }
        }
        elseif (strcasecmp($type, get_class($obj)) != 0) {
            $bad = true;
        }
        if ($bad) {
            trigger_error("Error: object wasn't of type $type at "
                    . "line $line in file '$file'", E_USER_ERROR);
        }
    } //}}}

    /* {{{variableDump
     * I have to do this for earlier versions of PHP in which there is no way to
     * just freakin' return the value from print_r()
     */
    function variableDump($object) {
        ob_start();
        print_r($object);
        $result = ob_get_contents();
        ob_end_clean();
        return ($result ? $result : "NULL");
    } // }}}

    function abortPage($message) {
        echo $message;
        exit;
    }
    
    /* {{{underlineAccessKey
     */
    function underlineAccessKey($string) {
        return preg_replace("/&(.)/", "<u>$1</u>", $string);
    } //}}}
    
    /* {{{getAccessKey
     */
    function getAccessKey($text) {
        $matches = array();
        preg_match("/&(.)/", $text, $matches);
        return (isset($matches[1]) ? $matches[1] : "");
    } //}}}

    /* {{{stripAccessKey
     */
    function stripAccessKey($text) {
        return str_replace("&", "", $text);
    } //}}}

    /* {{{redirect
     * Redirects if the headers haven't already been sent; if they have, shows
     * a link to the redirect url.
     */
    function redirect($url) {
        if (!headers_sent()) {
            header("Location:$url");
            exit;
        }
        else {
            Utility::abortPage("<h1>Redirect</h1><p>Please click on the"
                . " following link to go to your destination: <a href='"
                . "$url'>$url</a>.</p>");
        }
    } //}}}

    /* {{{getRandomString
     */
    function getRandomString ($length, $dictionary = null) {
        if (is_null ($dictionary)) {
            $dictionary = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        $dictionaryLength = strlen($dictionary);
        $result = "";
        for ($i = 0; $i < $length; ++$i) {
            $result .= substr($dictionary, rand(0, $dictionaryLength - 1), 1);
        }
        return $result;
    } //}}}

    /* {{{getFile
     */
    function getFile($fileName) {
        if (file_exists($fileName)) {
            return implode("", file($fileName));
        }
        else {
            echo "File $fileName does not exist!";
            exit;
        }
    } //}}}
    
    /* {{{highlightSql
     */
    function highlightSql($sql) {
        $gray = str_replace(" ", "\b|\b", 
            "ALL AND BETWEEN CROSS EXISTS JOIN IN LIKE NOT OR NULL OUTER SOME");

        $blue = str_replace(" ", "\b|\b", 
            "ADD ALTER AS ASC BIGINT BINARY BY CASCADE CHAR CHARACTER CHECK"
            . "COLLATE COLUMN COLUMNS CONNECTION CONSTRAINT CREATE CURRENT_DATE "
            . "CURRENT_TIME CURSOR DATABASE DEC DECIMAL DECLARE DEFAULT DELETE DESC "
            . "DESCRIBE CHANGE DISTINCT DROP ELSE ON EXPLAIN FALSE FOR FROM "
            . "GRANT GROUP HASH HAVING IF IGNORE INDEX INNER INSERT "
            . "INTERVAL INTO IS KEY LIMIT LOAD OPTIMIZE ORDER OUT RENAME REVOKE "
            . "SELECT SET SHOW TABLE THEN TO TRUE TRUNCATE UNION UNIQUE UPDATE USE "
            . "VALUES WHEN WHERE WHILE WITH");

        $pink = str_replace(" ", "\b|\b", 
            "CASE CURRENT_TIMESTAMP LEFT REPLACE RIGHT");

        $sql = preg_replace("/('|\")(.*?)(\\1)/", "<tt style='color:red'>\\1\\2\\3</tt>", $sql);
        $sql = preg_replace("/(\b$blue\b)/i", "<tt style='color:blue'>\\1</tt>", $sql);
        $sql = preg_replace("/(\b$pink\b)/i", "<tt style='color:#FF00FF'>\\1</tt>", $sql);
        return preg_replace("/(\b$gray\b)/i", "<tt style='color:gray'>\\1</tt>", $sql);
    } //}}}

}

?>
