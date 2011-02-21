<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker foldcolumn=2 tw=80 wrap:
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: SqlCommand.php,v 1.9 2005/06/17 05:35:11 outdoy1w Exp $
 *
 * Represents a command to execute against a SQL database, using a
 * SqlConnection.  There should be no need to subclass SqlCommand, as it's
 * abstract enough that the various SqlConnection subclasses should do the
 * work of translating to whatever format the particular database wants.
 *
 * SQL parameters may be embedded in the SQL text, in the format
 * {name,type,size,scale,nullable}.  Only the name and type are required;
 * nullable is used to specify whether a parameter with no specified value
 * should be replaced with null, and defaults to true.  An array of
 * name => value parameters may be used to replace these parameters in the
 * query with actual values, which will be sanitized according to type.
 */

require_once("SqlConnection.php");

class SqlCommand {

    var $conn = null;
    var $cmd = "";
    var $params = null;
    var $preparedQuery = "";

    function SqlCommand(&$conn, $cmd = null) {
        $this->cmd =& $cmd;
        $this->conn =& $conn;
    }

    function setConnection(&$conn) {
        $this->conn =& $conn;
        $this->preparedQuery = "";
    }

    function setCommandText($cmd) {
        $this->cmd = $cmd;
    }

    function getPreparedQuery() {
        if (!$this->preparedQuery) {
            $this->prepare();
        }
        return $this->preparedQuery;
    }

    /* {{{executeNonQuery
     * Returns the number of rows affected.
     */
    function executeNonQuery($params = array()) {
        $this->prepare($params);
        $res =& $this->conn->query($this->preparedQuery);
        return $res->rowsAffected();
    } //}}}

    /* {{{executeReader
     * Returns a SqlResult with the results of the query.
     */
    function &executeReader($params = array()) {
        $this->prepare($params);
        $res =& $this->conn->query($this->preparedQuery);
        return $res;
    } //}}}

    /* {{{executeScalar
     * Returns the first column of the first row of the results, or FALSE if
     * there are no results.
     */
    function executeScalar($params = array()) {
        $this->prepare($params);
        $res =& $this->conn->query($this->preparedQuery);
        return $res->fetchScalar();
    } //}}}

    /* {{{autoExecute
     * Accepts a table name, an array of colname => value values, a mode,
     * and an optional 'where' clause.  Mode can be DB_AUTOQUERY_INSERT
     * (that's the default) or DB_AUTOQUERY_UPDATE.
     */
    function autoExecute($table, $values = array(),
            $mode = DB_AUTOQUERY_INSERT, $where = false) {
        $query = "insert into $table set ";
        if ($mode == DB_AUTOQUERY_UPDATE) {
            $query = "update $table set ";
        }
        foreach ($values as $key => $val) {
            $query .= "$key = '" . addslashes($val) . "', ";
        }
        $query = substr($query, 0, -2);
        if ($where) {
            $query .= " where $where";
        }
        return $this->conn->query($query);
    } //}}}

    /* {{{prepare
     * Private method that parses through the command and replaces parameters
     * with values (sanitized).
     * $params is in the form array('name' => 'value').
     */
    function prepare($params = array()) {
        # Check that each parameter exists in the command, as a safeguard
        # against misspelled or wrong parameter replacement.
        foreach ($params as $name => $value) {
            if (strpos($this->cmd, "{" . $name) === false) {
                trigger_error("Parameter '$name' does not exist in query text",
                    E_USER_WARNING);
            }
        }
        $this->preparedQuery
            = preg_replace("/\{([a-zA-Z0-9_]+),([a-zA-Z]+)(?:,([0-9]+))?(?:,([0-9]+))?(?:,(\w+))?\}/e",
            "\$this->prepParam(\$params, '\$0', '\$1', '\$2', '\$3', '\$4', '\$5');", $this->cmd);
    } //}}}

    /* {{{prepParam
     * Private method that sanitizes a value, given its name, type, size, scale,
     * and whether it's nullable.
     */
    function prepParam(&$params, $fulltext, $name, $type, $size, $scale, $nullable) {
        $nullable = ($nullable !== '') ? $nullable : true;
        $size = intval($size);
        $scale = intval($scale);
        if (isset($params[$name]) && !is_null($params[$name])) {
            switch (strtolower($type)) {
            case "char":
            case "varchar":
                return "'"
                    . addslashes($size ? substr($params[$name], 0, $size) : $params[$name])
                    . "'";
            case "int":
            case "bigint":
            case "integer":
                return intval($params[$name]);
            case "numeric":
            case "decimal":
            case "float":
                return $scale
                    ? number_format(floatval($params[$name]), $scale, ".", "")
                    : floatval($params[$name]);
            case "date":
            case "datetime":
                return "'" . addslashes($params[$name]) . "'";
            }
        }
        return $nullable ? "null" : $fulltext;
    } //}}}

}

?>
