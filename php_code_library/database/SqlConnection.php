<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker foldcolumn=2 tw=80 wrap:
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: SqlConnection.php,v 1.9 2005/06/17 05:35:11 outdoy1w Exp $
 *
 * Represents a connection to a SQL database.
 */

define("DB_FETCHMODE_ASSOC", 0);
define("DB_FETCHMODE_ORDERED", 1);
define("DB_AUTOQUERY_INSERT", 0);
define("DB_AUTOQUERY_UPDATE", 1);

require_once("SqlCommand.php");

class SqlConnection {

    /* List of values for the options array:
     * user = db user
     * pass = db password
     * host = db hostname
     * db   = db database
     * port = db port
     * sock = db socket
     * persistent = bool; whether to use a persistent connection
     * errlevel = one of the PHP error levels.  When there's a DB error this is
     * the level of the error that will get triggered.
     */

    var $dbh = null;
    var $options = null;
    var $queries = null;

    function SqlConnection($options = null) {
        $this->queries = array();

        $this->options = array("db" => "");
        # Explicitly set some of the options; they might get overwritten but
        # need defaults
        $this->options['errlevel'] = E_USER_NOTICE;

        if ($options && is_array($options)) {
            foreach ($options as $key => $val) {
                $this->options[$key] = $val;
            }
        }
    }

    function open() {
        # Must be overridden in derived class.
    }

    function query($query, $params = null) {
        # Must be overridden in derived class.
    }

    function close() {
        # Must be overridden in derived class.
    }

    function addQuery($text) {
        $this->queries[] = array('text' => $text);
        return count($this->queries) - 1;
    }

    function setQueryStatus($num, $status, $messages, $info = null) {
        $this->queries[$num]['status'] = $status;
        $this->queries[$num]['messages'] = $messages;
        $this->queries[$num]['info'] = $info;
    }

    function &createCommand() {
        return new SqlCommand($this);
    }

    function changeDatabase($database) {
        $this->setOption("db", $database);
    }

    function getOption($name) {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    function setOption($name, $val) {
        $this->options[$name] = $val;
    }

    /* {{{getRawParams
     * Returns an array of parameters that haven't been replaced.
     */
    function &getRawParams($query) {
        preg_match_all("/\{([a-zA-Z0-9_]+)[^}]*}/",
            $query, $matches);
        return $matches[1];
    } //}}}

}

?>
