<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2003 Baron Schwartz.  This software is licensed under  |
 * | the terms of the GNU GPL.  See http://www.gnu.org for details.       |
 * +----------------------------------------------------------------------+
 * | Authors: Baron Schwartz <baron at sequent dot org>                   |
 * +----------------------------------------------------------------------+
 *
 * $Id: DateTime.php,v 1.11 2007/11/16 00:09:24 outdoy1w Exp $
 *
 * This class represents a date and time, down to the precision of one second.
 * The date is kept as a signed integer since the epoch.  Note that this will
 * not work on some systems, particularly Windows and older *nix systems.  See
 * the PHP documentation for date(), strtotime, and mktime() for more
 * information.
 */

class DateTime {

    var $time = 0;

    /* {{{Constructor
     */
    function DateTime($string = null, $time = null) {
        # Try to parse a string
        if (isset($string) && strlen($string) > 0
                && ($secs = $this->parseDate($string)) !== FALSE) {
            $this->time = $secs;
        }

        # Otherwise see if we were passed an integer representing the # of
        # seconds since the epoch
        elseif (isset($time)) {
            $this->time = $time;
        }

        # Otherwise, set the date according to the current system time.
        else {
            $this->time = time();
        }

    }//}}}

    /* {{{toString
     *
     */
    function toString($format = null) {

        # Return in ISO-compliant date-time format.
        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        return date($format, $this->time);
    }//}}}

    /* {{{getDaysInYear
     *
     * return number of days in the year
     */
    function getDaysInYear($year) {
        return ($year == 1752)
            ? 355
            : (($year & 3) == 0 && ($year % 100 || ($year % 400 == 0 && $year)))
                ? 366
                : 365;
    }//}}}

    /* {{{getDate
     *
     */
    function getDate() {
        return new DateTime(date("Y-m-d", $this->time));
    }//}}}

    /* {{{getDay
     *
     */
    function getDay() {
        return date("d", $this->time);
    }//}}}

    /* {{{getDayOfWeek
     *
     */
    function getDayOfWeek() {
        return date("w", $this->time);
    }//}}}

    /* {{{isLeapYear
	 * Years evenly divisible by four are normally leap years, except for...
	 * Years also evenly divisible by 100 are not leap years, except for...
	 * Years also evenly divisible by 400 are leap years.
     */
    function isLeapYear($year) {
        return (($year & 3) == 0 && ($year % 100 || ($year % 400 == 0 && $year)));
    }//}}}

    /* Is this date during daylight savings time?
     *
     */
    function isDST()
    {
        $localtime_res = localtime($this->time);
        return $localtime_res[8];
    }

    /* {{{getDayOfYear
     *
     */
    function getDayOfYear($year, $mon, $day) {
        return date("z", strtotime("$year-$mon-$day"));
    }//}}}

    function getDOY()
    {
       $rVal = $this->getDayOfYear($this->getYear(), $this->getMonth(), $this->getDay());
       #echo "RETURN: $rVal<br>";
       return $rVal;
    }

    /* {{{getHour
     *
     */
    function getHour() {
        return date("H", $this->time);
    }//}}}

    /* {{{getMinute
     *
     */
    function getMinute() {
        return date("i", $this->time);
    }//}}}

    /* {{{getMonth
     *
     */
    function getMonth() {
        return date("m", $this->time);
    }//}}}

    /* {{{now
     *
     */
    function now() {
        return new DateTime();
    }//}}}

    /* {{{getSecond
     *
     */
    function getSecond() {
        return date("s", $this->time);
    }//}}}

    /* {{{getTimeOfDay
     *
     */
    function getTimeOfDay() {
        return date("H", $this->time) * 3600
            + date("i", $this->time) * 60
            + date("s", $this->time);
    }//}}}

    /* {{{today
     *
     */
    function today() {
        return new DateTime(date("Y-m-d"));
    }//}}}

    /* {{{getYear
     *
     */
    function getYear() {
        return date("Y", $this->time);
    }//}}}

    /*
     * The following methods do not alter the DateTime object.  They return a
     * new object.  DateTime objects are immutable (unless you go tinkering
     * directly with the object's variables, which you should not do).
     */

    /* {{{addDays
     *
     */
    function addDays($days) {
        return new DateTime(null, mktime(
            date("H", $this->time),
            date("i", $this->time),
            date("s", $this->time),
            date("m", $this->time),
            date("d", $this->time) + $days,
            date("Y", $this->time)));
    }//}}}

    /* {{{addHours
     *
     */
    function addHours($hours) {
        return new DateTime(null, mktime(
            date("H", $this->time) + $hours,
            date("i", $this->time),
            date("s", $this->time),
            date("m", $this->time),
            date("d", $this->time),
            date("Y", $this->time)));
    }//}}}

    /* {{{addMinutes
     *
     */
    function addMinutes($mins) {
        return new DateTime(null, mktime(
            date("H", $this->time),
            date("i", $this->time) + $mins,
            date("s", $this->time),
            date("m", $this->time),
            date("d", $this->time),
            date("Y", $this->time)));
    }//}}}

    /* {{{addMonths
     *
     */
    function addMonths($mons) {
        return new DateTime(null, mktime(
            date("H", $this->time),
            date("i", $this->time),
            date("s", $this->time),
            date("m", $this->time) + $mons,
            date("d", $this->time),
            date("Y", $this->time)));
    }//}}}

    /* {{{addSeconds
     *
     */
    function addSeconds($secs) {
        return new DateTime(null, mktime(
            date("H", $this->time),
            date("i", $this->time),
            date("s", $this->time) + $secs,
            date("m", $this->time),
            date("d", $this->time),
            date("Y", $this->time)));
    }//}}}

    /* {{{addYears
     *
     */
    function addYears($years) {
        return new DateTime(null, mktime(
            date("H", $this->time),
            date("i", $this->time),
            date("s", $this->time),
            date("m", $this->time),
            date("d", $this->time),
            date("Y", $this->time) + $years));
    }//}}}

    /* {{{compare
     *
     */
    function compare($dt1, $dt2) {
        return $dt1->compareTo($dt2);
    }//}}}

    /* {{{compareTo
     * Compares $this to $otherDT.  If $this is greater, returns 1.
     */
    function compareTo($otherDT) {
        return ($this->time == $otherDT->time
            ? 0
            : ($this->time > $otherDT->time
                ? 1
                : -1));
    }//}}}


    /* {{{equals
     *
     */
    function equals($otherDT) {
        return ($this->time == $otherDT->time);
    }//}}}

    /* {{{parse
     *
     */
    function parse($string) {
        return new DateTime($string);
    }//}}}

    /* {{{parseDate
     * Recognizes the following formats, in decreasing order of strictness:
     *   MySQL timestamp: 14 digits
     *   Anything that strtotime() can parse
     */
    function parseDate($string) {

        # MySQL timestamp
        if (preg_match("/^\d{14}$/", $string)) {
            return strtotime(sprintf("%04d-%02d-%02d %02d:%02d:%02d",
                    substr($string, 0, 4),
                    substr($string, 4, 2),
                    substr($string, 6, 2),
                    substr($string, 8, 2),
                    substr($string, 10, 2),
                    substr($string, 12, 2)));
        }

        $result = strtotime($string);

        if ($result === -1) {
            trigger_error("Warning: possible error parsing $string into a date", E_USER_NOTICE);
        }

        return $result;
    }//}}}


    // toSecondsSinceEpoch
    // return this date represent as seconds since midnight GMT, Jan 1 1970 (no
    // leap seconds)
    function toSecondsSinceEpoch()
    {
        return $this->time;
    }


    function toDaysSinceEpoch()
    {
        return ($this->time / 86400); 
    }

    function setYear($year)
    {
        $yearsToAdd = $year - $this->getYear();
        return $this->addYears($yearsToAdd);
    }

}
?>
