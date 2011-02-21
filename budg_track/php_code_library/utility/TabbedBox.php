<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker foldcolumn=2 tw=80 wrap:
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: TabbedBox.php,v 1.5 2005/06/17 05:35:11 outdoy1w Exp $
 *
 * Purpose: A box with multiple tabs across the top.  Each tab has some text in it,
 * which could be a link as well.
 *
 * The data structure of a tabbed box looks something like this:
 * class TabbedBox
 *  |
 *  +-array tabRows
 *          |
 *          +[0]--+
 *          |     +[Google]
 *          |     |  +[accesskey] => "G"
 *          |     |  +[label] => "<u>G</u>oogle"
 *          |     |  +[link] => "http://www.google.com"
 *          |     |  +[width] => 100
 *          |     |  +[tooltip] => "Go to Google"
 *          |     +[MSN]
 *          |        +[accesskey] => "M"
 *          |        +[label] => "<u>M</u>SN"
 *          |        +[link] => "http://www.msn.com"
 *          |        +[width] => 
 *          |        +[tooltip] => "Never go here!"
 *          +[1]--+
 *                +[Yahoo]
 *                   +[accesskey] => "Y"
 *                   +[label] => "<u>Y</u>ahoo"
 *                   +[link] => "http://www.yahoo.com"
 *                   +[width] =>
 *                   +[tooltip] => "Go to Yahoo"
 * Basically, it's a multi-dimensional array.  The top level is an
 * array of rows; you usually want to have one row.  The next level is
 * an array of tabs; each tab is an array of accesskey, label, link,
 * and tooltip.  The system will automatically decide which row to
 * place on the bottom, depending on which one has the active tab.
 */

class TabbedBox extends Object{
    // {{{declarations
    var $tabRows    = array();  # An array of rows of tabs
    var $activeTab  = "";       # The currently active tab, which is highlighted
    var $contents   = "";       # The contents of the box
    var $style      = "";       # The box's background color
    // }}}

    /* {{{constructor
     *
     */
    function TabbedBox($tabs = array()) {
        if (!is_array($tabs)) {
            trigger_error("Parameter was not an array", E_USER_ERROR);
        }
        $this->tabs = $tabs;
    } //}}}

    /* {{{setActiveTab
     * Sets the active tab to the element that is keyed on $activeTab
     */
    function setActiveTab($activeTab) {
        $this->activeTab = $activeTab;
    } //}}}

    /* {{{getActiveTab
     * returns the active tab.
     */
    function getActiveTab() {
        return $this->activeTab;
    } //}}}

    /* {{{setStyle
     */
    function setStyle($color) {
        $this->style = $color;
    } //}}}

    /* {{{getStyle
     */
    function getStyle() {
        return $this->style;
    } //}}}

    /* {{{setTabs
     * Sets the tabs array
     */
    function setTabs($tabs) {
        $this->tabs = $tabs;
    } //}}}

    /* {{{getTabs
     * Returns an array of tabs.
     */
    function getTabs() {
        return $tabs;
    } //}}}

    /* {{{addTab
     * Adds another tab as key (label) and text
     */
    function addTab($text, $link, $row = 0, $tooltip = "", $width = "") {
        $name = Utility::stripAccessKey($text);
        $this->tabRows[$row][$name] = array(
            'accesskey' => Utility::getAccessKey($text),
            'label' => str_replace(' ', '&nbsp;', Utility::underlineAccessKey($text)),
            'link' => $link,
            'width' => $width,
            'tooltip' => $tooltip);
    } //}}}

    /* deleteTab
     * Deletes the tab with the specified label.
     */
    function deleteTab($label) {
        foreach ($this->tabRows as $key => $row) {
            unset($this->tabRows[$key][$label]);
        }
    } #}}}

    /* {{{setContents
     * Sets the contents of the box.
     */
    function setContents($contents) {
        $this->contents = $contents;
    } //}}}

    /* {{{getContents
     * Returns the contents of the box.
     */
    function getContents() {
        return $this->contents;
    } //}}}

    /* {{{addToContents
     * Adds to the contents of the box, allowing for more later (which
     * setContents does not do)
     */
    function addToContents($contents) {
        $this->contents .= $contents;
    } //}}}

    /* {{{toString
     * Returns an HTML string representation of the whole thing.
     */
    function toString() {
        global $obj;
        $tabRowTemplate = '<table cellspacing="0" cellpadding="3" border="0"'
            . 'width="100%">{ROW}</table>';
        $tabTemplate = '<td class="{CLASS}" align="center" width="{WIDTH}">'
            . '<a title="{TOOLTIP}" accesskey="{ACCESSKEY}" href="{LINK}">{LABEL}</a></td>';

        $box = Utility::getFile("templates/misc/tabbed-box.php");
        $tabs = array();
        $bottomRow = "";
        $tabHTML = "";
        $activeRow = 0;
        foreach ($this->tabRows as $row => $arrayOfTabs) {
            $tabs[$row] = "";
            foreach ($arrayOfTabs as $name => $tab) {
                if ($name == $this->getActiveTab()) {
                    $class = "active";
                    $activeRow = $row;
                }
                else {
                    $class = "inactive";
                }
                $tabs[$row] .= Template::replaceValues($tabTemplate, array(
                    'CLASS' => $class,
                    'ACCESSKEY' => $tab['accesskey'],
                    'LINK' => $tab['link'],
                    'TOOLTIP' => $tab['tooltip'],
                    'WIDTH' => $tab['width'],
                    'LABEL' => $tab['label']));
            }
        }
        $bottomRow = Template::replaceValues($tabRowTemplate, array(
            "ROW" => $tabs[$activeRow]));
        unset($tabs[$activeRow]);
        foreach ($tabs as $index => $tab) {
            $tabHTML .= Template::replaceValues($tabRowTemplate, array(
                "ROW" => $tab));
        }
        return Template::replaceValues($box, array(
            "TABS" => $tabHTML . $bottomRow,
            "STYLE" => $this->getStyle(),
            "CONTENTS" => $this->getContents()));
    } //}}}

}

?>
