<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Core;

use Carbon\Carbon;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTP;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use TitleDK\Calendar\Events\Event;

/**
 * Calendar Helper
 * Helper class for calendar related calculations
 *
 * @package calendar
 * @subpackage core
 */
class CalendarHelper
{
    /**
     * @param array<\TitleDK\Calendar\Calendars\Calendar> $calendars
     * @param bool $returnCSV true to return CSV, false not to
     * @return array<int> Calendar IDs
     */
    public static function getValidCalendarIDsForCurrentUser(array $calendars, bool $returnCSV = false): array
    {
        $member = Security::getCurrentUser();
        $memberGroups = [];
        if (isset($member)) {
            foreach ($member->Groups() as $group) {
                $memberGroups[$group->ID] = $group->ID;
            }
        }

        $calendarIDs = [];
        // add calendar if not group restricted
        foreach ($calendars as $calendar) {
            $groups = $calendar->Groups();
            if ($groups->Count() > 0) {
                foreach ($groups as $group) {
                    if (!\in_array($group->ID, $memberGroups, true)) {
                        continue;
                    }

                    $calendarIDs[] = $calendar->ID;
                }
            } else {
                $calendarIDs[] = $calendar->ID;
            }
        }

        if ($returnCSV) {
            $calendarIDs = \implode(',', $calendarIDs);
        }

        return $calendarIDs;
    }


    /**
     * Get all coming public events
     *
     * @TODO is this parameter type correct?
     * @return \SilverStripe\ORM\DataList<\TitleDK\Calendar\Events\Event>
     */
    public static function comingEvents(bool $from = false): DataList
    {
        $time = ($from ? \strtotime($from) : Carbon::now()->timestamp);
        $sql = "(\"StartDateTime\" >= '".\date('Y-m-d', $time)." 00:00:00')";

        return Event::get()->where($sql);
    }


    /**
     * Get all coming public events - with optional limit
     *
     * @return \SilverStripe\ORM\DataList<\TitleDK\Calendar\Events\Event>
     */
    public static function comingEventsLimited(bool $from = false, int $limit = 30): DataList
    {
        return self::comingEvents($from)->limit($limit);
    }


    /**
     * Get all past public events
     *
     * @return \SilverStripe\ORM\DataList<\TitleDK\Calendar\Events\Event>
     */
    public static function pastEvents(): DataList
    {
        return Event::get()
            ->filter(
                [
                    'StartDateTime:LessThan' => \date('Y-m-d', Carbon::now()->timestamp),
                ],
            );
    }


    /**
     * Get all events
     *
     * @return \SilverStripe\ORM\DataList<\TitleDK\Calendar\Events\Event>
     */
    public static function allEvents(): DataList
    {
        return Event::get();
    }


    /**
     * Get all events - with an optional limit
     *
     * @param int $limit the maximum number of results to return
     * @return \SilverStripe\ORM\DataList<\TitleDK\Calendar\Events\Event>
     */
    public static function allEventsLimited(int $limit = 30): DataList
    {
        return self::allEvents()->limit($limit);
    }


    /***
     * Get events for a specific month
     * Format: 2013-07
     *
     * @param array<int>|string $calendarIDs optional CSV or array of calendar ID to filter by
     */
    public static function eventsForMonth(string $month, $calendarIDs = []): DataList
    {
        // @todo method needs fixed everywhere to pass in an array of IDs, not a CSV
        if (!\is_array($calendarIDs)) {
            $calendarIDs = \explode(',', $calendarIDs);
            \user_error('events for month called with ID instead of array of calendar IDs');
        }

        $nextMonth = \strtotime('last day of this month', \strtotime($month));

        $currMonthStr = \date('Y-m-d', \strtotime($month));
        $nextMonthStr = \date('Y-m-d', $nextMonth);

        return self::eventsForDateRange($currMonthStr, $nextMonthStr, $calendarIDs);
    }


    /**
     * @param string $startDateStr start date in format 2018-05-15
     * @param string $endDateStr ditto end date
     * @param array<int> $calendarIDs
     * @param array<int> $calendarIDS list of calendar IDs visible
     * @return \TitleDK\Calendar\Core\DataList<\TitleDK\Calendar\Events\Event>
     */
    public static function eventsForDateRange(
        string $startDateStr,
        string $endDateStr,
        array $calendarIDs = []
    ): \SilverStripe\ORM\DataList {
        $endDateStr .= ' 23:59:59';
        $sql = "((\"StartDateTime\" BETWEEN '$startDateStr' AND '$endDateStr') OR (\"EndDateTime\" BETWEEN
        '$startDateStr' AND '$endDateStr'))";

        $events = Event::get()
            ->where($sql);

        // optional filter by calendar id
        if (\count($calendarIDs) > 0) {
            $events = $events->filter('CalendarID', $calendarIDs);
        }

        return $events;
    }


    /**
     * If applicable, adds preview parameters. ie. CMSPreview and SubsiteID.
     *
     * @param string $link original link
     * @param \TitleDK\Calendar\Calendars\Calendar $calendar a calendar, with possibly a subsite ID
     */
    public static function addPreviewParams(string $link, Calendar $calendar): string
    {
        // Pass through if not logged in
        if (!Member::currentUserID()) {
            return $link;
        }
        $modifiedLink = '';
        $request = Controller::curr()->getRequest();
        if ($request && $request->getVar('CMSPreview')) {
            // Preserve the preview param for further links
            $modifiedLink = HTTP::setGetVar('CMSPreview', 1, $link);
            // Quick fix - multiple uses of setGetVar method double escape the ampersands
            $modifiedLink = \str_replace('&amp;', '&', $modifiedLink);
            // Add SubsiteID, if applicable
            if (isset($calendar->SubsiteID)) {
                $modifiedLink = HTTP::setGetVar('SubsiteID', $calendar->SubsiteID, $modifiedLink);
                // Quick fix - multiple uses of setGetVar method double escape the ampersands
                $modifiedLink = \str_replace('&amp;', '&', $modifiedLink);
            }
        }

        return ($modifiedLink)
            ? $modifiedLink
            : $link;
    }
}
