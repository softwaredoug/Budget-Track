<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker foldcolumn=2 tw=80 wrap:
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: MySqlResult.php,v 1.8 2007/07/29 20:55:40 outdoy1w Exp $
 *
 * Represents a result-set from a MySQL query.
 */

require_once("SqlResult.php");

class MySqlResult extends SqlResult {

    function MySqlResult($sth, $query, $rowsAffected, $info = null, $identity = null) {
        $this->SqlResult($sth, $query, $rowsAffected, $info, $identity);
    }

    function numRows() {
        if ($this->sth) {
            return mysql_num_rows($this->sth);
        }
        return 0;
    }

    function numCols() {
        if ($this->sth) {
            $num_fields = mysql_num_fields($this->sth);
            return $num_fields; 
        }
        return 0;
    }

    function &fetchRow($fetchMode = null) {
        $mode = is_null($fetchMode) ? DB_FETCHMODE_ASSOC : $fetchMode;
        if ($this->sth) {
            switch ($mode) {
            case DB_FETCHMODE_ORDERED:
                $fetchRes = mysql_fetch_row($this->sth);
                return $fetchRes;
            default:
                $fetchRes = mysql_fetch_assoc($this->sth);
                return $fetchRes; 
            }
        }
        return array();
    }

    function fetchScalar() {
        if ($this->numRows() && $this->numCols()) {
            $row =& $this->fetchRow(DB_FETCHMODE_ORDERED);
            return $row[0];
        }
        return FALSE;
    }

    function seekRow($row) {
        if ($this->sth) {
            $row = intval($row);
            if ($row < 0 || $row >= $this->numRows()) {
                trigger_error("Cannot move to that row", E_USER_WARNING);
            }
            else {
                return mysql_data_seek($this->sth, $row);
            }
        }
        return false;
    }

}
?>
