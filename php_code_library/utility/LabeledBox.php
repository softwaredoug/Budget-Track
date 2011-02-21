<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker foldcolumn=2 tw=80 wrap:
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: LabeledBox.php,v 1.6 2005/06/17 05:35:11 outdoy1w Exp $
 *
 * Purpose:  A box with tab in the upper left corner that contains text.
 */

class LabeledBox extends Object{
    // {{{declarations
    var $label;
    var $contents;
    // }}}

    /* {{{setLabel
     * Set the text of the label
     */
    function setLabel($label) {
        $this->label = $label;
    } //}}}

    /* {{{getLabel
     * Get the text of the label
     */
    function getLabel() {
        return $this->label;
    } //}}}

    /* {{{setContents
     * Set the contents of the box
     */
    function setContents($contents) {
        $this->contents = $contents;
    } //}}}

    /* {{{getContents
     * Get the contents of the box
     */
    function getContents() {
        return $this->contents;
    } //}}}

    /* {{{addToContents
     * Add something to the contents of the box, leaving open the possibility of
     * adding more later (unlike setContents)
     */
    function addToContents($contents) {
        $this->contents .= $contents;
    } //}}}

    /* {{{toString
     * Create an HTML representation of the box and return it
     */
    function toString() {
        global $obj;
        $box = Utility::getFile("templates/misc/labeled-box.php");
        return Template::replaceValues($box, array(
            "LABEL" => $this->getLabel(), 
            "CONTENTS" => $this->getContents()));
    } //}}}

}

?>
