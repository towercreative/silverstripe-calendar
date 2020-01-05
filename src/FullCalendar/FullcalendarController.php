<?php
namespace TitleDK\Calendar\FullCalendar;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Security;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Events\Event;

/**
 * Fullcalendar controller
 * Controller/API, used for interacting with the fullcalendar js plugin
 *
 * @package calendar
 * @subpackage fullcalendar
 */
class FullcalendarController extends Controller
{

    protected $event = null;
    protected $start = null;
    protected $end = null;
    protected $allDay = false;
    protected $member = null;


    private static $allowed_actions = array(
        'events',
    );


    public function init()
    {
        parent::init();

        $this->member = Security::getCurrentUser();

        $request = $this->getRequest();

        //Setting dates based on request variables
        //We could add some sanity check here
        $this->start = $request->getVar('start');
        $this->end = $request->getVar('end');

        // @todo This does not appear to be used
        if ($request->getVar('allDay') == 'true') {
            $this->allDay = true;
        }

        //Setting event based on request vars
        if (($eventID = (int) $request->getVar('eventID')) && ($eventID > 0)) {
            $event = Event::get()
                ->byID($eventID);
            if ($event && $event->exists()) {
                $this->event = $event;
            }
        }
    }


    /**
     * Calculate start/end date for event list
     * Currently set to offset of 30 days
     *
     * @param string $type      ("start"/"end")
     * @param int    $timestamp
     * return \SS_Datetime
     */
    public function eventlistOffsetDate($type, $timestamp, $offset = 30)
    {
        return self::offset_date($type, $timestamp, $offset);
    }

    /**
     * Calculate start/end date for event list
     * @todo this should go in a helper class
     *
     * @param string  $type
     * @param integer $timestamp
     */
    public static function offset_date($type, $timestamp, $offset = 30)
    {
        if (!$timestamp) {
            $timestamp = time();
        }

        // check whether the timestamp was
        // given as a date string (2016-09-05)
        if (strpos($timestamp, "-") > 0) {
            $timestamp = strtotime($timestamp);
        }

        $offsetCalc = $offset * 24 * 60 * 60; //days in secs

        $offsetTime = null;
        if ($type == 'start') {
            $offsetTime = $timestamp - $offsetCalc;
        } elseif ($type == 'end') {
            $offsetTime = $timestamp + $offsetCalc;
        }

        $str = date('Y-m-d', $offsetTime);
        return $str;
    }

    /**
     * Handles returning the JSON events data for a time range.
     *
     * @param  HTTPRequest $request
     * @return HTTPResponse
     */
    public function events($request, $json = true)
    {
        /**
 * @var string $calendars comma separated list of calendar IDs to show events for
*/
        $calendars = $request->getVar('calendars');

        /**
 * @var string $offset days to offset by
*/
        $offset = empty($request->getVar('offset')) ? 30 : $request->getVar('offset');

        $filter = array(
            'StartDateTime:GreaterThan' => $this->eventlistOffsetDate('start', $request->postVar('start'), $offset),
            'EndDateTime:LessThan' => $this->eventlistOffsetDate('end', $request->postVar('end'), $offset),
        );

        // start a query for events
        $events = Event::get()
            ->filter(
                $filter
            );

        // filter by calendar ids if they have been provided
        if ($calendars) {
            $calIDList = explode(',', $calendars);
            $events = $events->filter('CalendarID', $calIDList);
        }

        $result = array();
        if ($events->count() > 0) {
            foreach ($events as $event) {
                $calendar = $event->Calendar();

                $bgColor = '#999'; //default
                $borderColor = '#555';

                // @todo This is an error in that it enforces use of the color extension.  May as well just have it
                // there by default
                if ($calendar->exists()) {
                    $bgColor = $calendar->getColorWithHash();
                    $borderColor = $calendar->getColorWithHash();
                }

                $resultArr = self::format_event_for_fullcalendar($event);
                $resultArr = array_merge(
                    $resultArr,
                    array(
                    'backgroundColor' => $bgColor,
                    'textColor' => '#FFF',
                    'borderColor' => $borderColor,
                    )
                );
                $result[] = $resultArr;
            }
        }

        if ($json) {
            $response = new HTTPResponse(json_encode($result));
            $response->addHeader('Content-Type', 'application/json');
            return $response;
        } else {
            return $result;
        }
    }


    /**
     * AJAX Json Response handler
     *
     * @param  array|null $retVars
     * @param  boolean    $success
     * @return HTTPResponse
     */
    public function handleJsonResponse($success = false, $retVars = null)
    {
        $result = array();
        if ($success) {
            $result = array(
                'success' => $success
            );
        }
        if ($retVars) {
            $result = array_merge($retVars, $result);
        }

        $response = new HTTPResponse(json_encode($result));
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Format an event to comply with the fullcalendar format
     *
     * @todo Move to a helper
     *
     * @param Event $event
     */
    public static function format_event_for_fullcalendar($event)
    {
        $bgColor = '#999'; //default
        $borderColor = '#555';

        $arr = array(
            'id'        => $event->ID,
            'title'     => $event->Title,
            'start'     => self::format_datetime_for_fullcalendar($event->StartDateTime),
            'end'       => self::format_datetime_for_fullcalendar($event->EndDateTime),
            'allDay'        => $event->isAllDay(),
            'className' => $event->ClassName,
            //event calendar
            'backgroundColor' => $bgColor,
            'textColor' => '#FFFFFF',
            'borderColor' => $borderColor,
        );
        return $arr;
    }

    /**
     * Format SS_Datime to fullcalendar format
     *
     * @todo Move to a helper
     *
     * @param string $datetime
     */
    public static function format_datetime_for_fullcalendar($datetime)
    {
        $time = strtotime($datetime);
        $str = date('c', $time);

        return $str;
    }
}
