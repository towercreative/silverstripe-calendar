<?php
namespace TitleDK\Calendar\Libs;

use SilverStripe\Dev\Debug;

//from https://gist.github.com/dominikzogg/1578524
//Se in the bottom of this file for the SilverStripe Calendar ICSExport_Controller




/**
 * Distributed under the GNU Lesser General Public License (LGPL v3)
 * (http://www.gnu.org/licenses/lgpl.html)
 * This program is distributed in the hope that it will be useful -
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @author    Dominik Zogg <dominik.zogg@gmail.com>
 * @copyright Copyright (c) 2011, Dominik Zogg
 */

class ICSExport
{
    /**
     * @var array $_arrCalendar the calendar array
     */
    protected $_arrCalendar = array();

    /**
     * @var string $_strIcsHeader the header of the calendar
     */
    protected $_strIcsHeader = "BEGIN:VCALENDAR\r\nPRODID:-//php/ics\r\nVERSION:2.0\r\nMETHOD:PUBLISH\r\n";

    /**
     * @var string $_strIcsFooter the footer of the calendar
     */
    protected $_strIcsFooter = 'END:VCALENDAR';

    /**
     * @var string $_strIcs ics string
     */
    protected $_strIcs = '';

    /**
     * __construct
     *
     * @param array $arrCalendar the calendar as an array
     */
    public function __construct(array $arrCalendar)
    {
        $this->_arrCalendar = $arrCalendar;
        $this->_strIcs = $this->_strIcsHeader;
        foreach ($this->_arrCalendar as $arrEvent) {
            $this->_strIcs .= self::generateEventString($arrEvent);
        }
        $this->_strIcs .= $this->_strIcsFooter;
    }

    /**
     * getFile
     *
     * @param string $strFilename the names of the file
     */
    public function getFile($strFilename)
    {
        ob_start();
        header("Content-type: text/calendar");
        header('Content-Disposition: attachment; filename="' .  $strFilename . '"');
        echo $this->_strIcs;
        ob_flush();
        die();
    }

    /**
     * getString
     *
     * @return string ics string
     */
    public function getString()
    {
        return $this->_strIcs;
    }


    public static function ics_date($str, $allDay)
    {
        $time = strtotime($str);

            //this sets the time according to server locale
            //this is with time zone - but needs the locale to be set properly on the server
            //it wasn't on my mac
            //$date = gmstrftime("%Y%m%dT%H%M00Z", $str);

            //event without time zone - see:
            //http://stackoverflow.com/questions/7626114/ics-timezone-not-working
        if ($allDay) {
            //see discussion about allday events here:
            //http://stackoverflow.com/questions/1716237/single-day-all-day-appointments-in-ics-files
            $date = strftime("%Y%m%d", $time);
        } else {
            $date = strftime("%Y%m%dT%H%M00", $time);
        }


            //$date = gmstrftime("%Y%m%dT%H%M00Z", $time);
            return $date;
    }

    /**
     * generateEventString
     *
     * @param  array $arrEvent
     * @return string event as ics string
     */
    public static function generateEventString(array $arrEvent)
    {
        $strReturn = "BEGIN:VEVENT\r\n";
        $arrEventParts = array();

        // set uid
        if (isset($arrEvent['ID'])) {
            $arrEventParts['UID'] = md5($arrEvent['ID'] . "@" . $_SERVER['SERVER_NAME']);
        }

        // set creation date
        if (isset($arrEvent['Created'])) {
            $arrEventParts['DTSTAMP'] = self::ics_date($arrEvent['Created'], $arrEvent['AllDay']);
        } elseif (isset($arrEvent['StartDateTime'])) {
            $arrEventParts['DTSTAMP'] = self::ics_date($arrEvent['StartDateTime'], $arrEvent['AllDay']);
        }

        // set start time of the event
        if (isset($arrEvent['StartDateTime'])) {
            $arrEventParts['DTSTART'] = self::ics_date($arrEvent['StartDateTime'], $arrEvent['AllDay']);
        }

        // set end time of the event
        if (isset($arrEvent['EndDateTime'])) {
            $arrEventParts['DTEND'] = self::ics_date($arrEvent['EndDateTime'], $arrEvent['AllDay']);
        }

        // set summary
        if (isset($arrEvent['Title'])) {
            $arrEventParts['SUMMARY'] = self::cleanString($arrEvent['Title']);
        }

        // set description
        if (isset($arrEvent['Details'])) {
            $arrEventParts['DESCRIPTION'] = self::cleanString($arrEvent['Details']);
        }

        // set location
        //        if(isset($arrEvent['location']))
        //        {
        //            $arrEventParts['LOCATION'] = self::cleanString($arrEvent['location']);
        //        }

                //setting end date = start date if no end date is present
                //TODO: warning, or log here
        if (!isset($arrEventParts['DTEND'])) {
            $arrEventParts['DTEND'] = $arrEventParts['DTSTART'];
        }
                //setting start date = end date if no start date is present
                //TODO: warning, or log here
        if (!isset($arrEventParts['DTSTART'])) {
            $arrEventParts['DTSTART'] = $arrEventParts['DTEND'];
        }


        // check if all needed values are set if not throw exception
        if (!isset($arrEventParts['UID'])
            || !isset($arrEventParts['DTSTAMP'])
            || !isset($arrEventParts['DTSTART'])
            || !isset($arrEventParts['DTEND'])
            || !isset($arrEventParts['SUMMARY'])
        ) {
            Debug::dump($arrEventParts);
            throw new \Exception('at least one missing value');
        }

        // add event parts to return string
        foreach ($arrEventParts as $strKey => $strValue) {
            $strReturn .= $strKey . ":" . $strValue . "\r\n";
        }

        // add end to return string
        $strReturn .= "END:VEVENT" . "\r\n";

        // return event string
        return($strReturn);
    }

    /**
     * cleanString
     *
     * @param  string $strDirtyString the dirty input string
     * @return string cleaned string
     */
    public static function cleanString($strDirtyString)
    {
        $arrBadSigns = array('<br />', '<br/>', '<br>', "\r\n", "\r", "\n", "\t", '"');
        $arrGoodSigns = array('\n', '\n', '\n', '', '', '', ' ', '\"');
        return(trim(str_replace($arrBadSigns, $arrGoodSigns, strip_tags($strDirtyString, '<br>'))));
    }


        /**
         * returns an ICSExport calendar object by supplying a Silverstripe calendar
         *
         * @param type $cal
         */
    public static function ics_from_sscal($cal)
    {
        $events = $cal->Events();
        $eventsArr = $events->toNestedArray();

        //Debug::dump($eventsArr);
        //return false;
        $ics = new ICSExport($eventsArr);
        return $ics;
    }
}
