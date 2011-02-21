<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker foldcolumn=2 tw=80 wrap:
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: SqlResult.php,v 1.8 2005/06/17 05:35:11 outdoy1w Exp $
 *
 * Represents a result-set from a SQL query.
 */

class SqlResult {

    var $query = null;
    var $sth = null;
    var $info = null;
    var $identity = null;
    var $rows = null;

    function SqlResult($sth, $query, $rowsAffected, $info = null, $identity = null) {
        $this->sth = $sth;
        $this->query = $query;
        $this->info = $info;
        $this->identity = $identity;
        $this->rows = $rowsAffected;
    }

    function numRows() {
        # Must be implemented in subclasses
    }

    function numCols() {
        # Must be implemented in subclasses
    }

    function rowsAffected() {
        return $this->rows ? $this->rows : FALSE;
    }

    function identity() {
        return $this->identity ? $this->identity : FALSE;
    }

    function &fetchRow($fetchMode) {
        # Must be implemented in subclasses
    }

    function fetchScalar() {
        # Must be implemented in subclasses
    }

    function seekRow($row) {
        # Must be implemented in subclasses
    }

}
?>
