<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker foldcolumn=2 tw=80 wrap:
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: MySqlConnection.php,v 1.11 2007/07/29 20:55:54 outdoy1w Exp $
 *
 * Represents a connection to a MySQL database.
 */

require_once("SqlConnection.php");
require_once("MySqlResult.php");

class MySqlConnection extends SqlConnection {

    function MySqlConnection($options = null) {
        $this->SqlConnection($options);
    }

    function open() {
        $portSock = $this->getOption("port")
            ? (":" .  $this->getOption("port"))
            : "";
        $portSock = (!$portSock && $this->getOption("sock"))
            ? (":" .  $this->getOption("sock"))
            : "";
        if ($this->getOption("persistent")) {
            $this->dbh = mysql_pconnect($this->getOption("host") . $portSock,
                $this->getOption("user"),
                $this->getOption("pass"));
        }
        else {
            $this->dbh = mysql_connect($this->getOption("host") . $portSock,
                $this->getOption("user"),
                $this->getOption("pass"));
        }
        if (!$this->dbh) {
            die("help");
            trigger_error("Could not connect to database: " . mysql_error(), E_USER_ERROR);
            return false;
        }
        if (!mysql_select_db($this->getOption("db"), $this->dbh)) {
            trigger_error("Could not select database: " . mysql_error(), E_USER_ERROR);
            return false;
        }
    }

    /* {{{query
     * Send the query to the database, possibly taking advantage of a SqlCommand
     * as an intermediary.
     */
    function &query($query, $params = null) {
        # Array of errors that happen while executing the query
        $errors = array();

        if (!$this->dbh) {
            trigger_error("Connection is not open", E_USER_ERROR);
            return;
        }

        # TODO: parse query into multiple semicolon-delimited queries and send
        # each to the DB separately, chaining result sets together.

        # If the user sent parameter replacement values, the query should be
        # prepared before sending to the database.  The correct way to do this
        # is to create a command and execute it with the parameters.  That
        # object will prepare the query text and actually call this function
        # again with the result, which this function will pass right through to
        # the database.
        if ($params) {
            $cmd =& $this->createCommand();
            $cmd->setCommandText($query);
            $cmdRes =  $cmd->executeReader($params);
            return $cmdRes;
        }
	
        # Check for parameters that haven't yet been replaced in the query text
        $raw = $this->getRawParams($query);
        if (count($raw)) {
            $rawErrors = "You have not replaced all parameters in your "
                . "query.  The following parameters remain: {" 
                . implode(", ", $raw) . "}";
            $errors[] = $rawErrors;
            trigger_error($rawErrors, $this->getOption("errlevel"));
        }

        $queryNum = $this->addQuery($query);
        $sth = mysql_query($query, $this->dbh);
        # Possibly trigger an error
        if (!$sth) {
            $smtErr = mysql_error($this->dbh);
            $errors[] = $smtErr;
            if ($this->getOption("errlevel")) {
                trigger_error("SQL Error in '$query': $smtErr",
                    $this->getOption("errlevel"));
            }
        }
        $ident = mysql_insert_id($this->dbh);
        $rows = mysql_affected_rows($this->dbh);
        $info = mysql_info($this->dbh);
        $this->setQueryStatus($queryNum, mysql_errno($this->dbh), $errors);
        $res = new MySqlResult($sth, $query, $rows, $info, $ident);
        return $res;
    } //}}}

    function close() {
        if ($this->dbh && !$this->getOption("persistent")) {
            mysql_close($this->dbh);
            $this->dbh = null;
        }
    }

    function &createCommand() {
        $command = new SqlCommand($this);
        return $command;
    }

    function changeDatabase($database) {
        $this->setOption("db", $database);
    }

    function getOption($name) {
        $issetRes = isset($this->options[$name]) ? $this->options[$name] : null;
        return $issetRes;
    }

    function setOption($name, $val) {
        $this->options[$name] = $val;
    }

}

?>
