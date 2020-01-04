<?php
namespace TitleDK\Calendar\Events;

use Carbon\Carbon;
use SilverStripe\ORM\FieldType\DBDatetime;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;

/**
 * Event Helper
 * Helper class for event related calculations and formatting
 *
 * @package calendar
 */
class EventHelper
{
    use DateTimeHelperTrait;

    /**
     * Date format for YMD
     *
     * @todo move to config
     */
    const YMD_DATE_FORMAT='Y-m-d';

    /**
     * Formatted Dates
     * Returns either the event's date or both start and end date if the event spans more than
     * one date
     *
     * Format:
     * Jun 7th - Jun 10th
     *
     * @param  DBDatetime $startObj
     * @param  DBDatetime $endObj
     * @return string
     */
    public static function formatted_dates($startObj, $endObj)
    {
        //Checking if end date is set
        $endDateIsset = !empty($endObj->getValue());

        $startTime = strtotime($startObj->value);
        $endTime = strtotime($endObj->value);

        $startMonth = date('M', $startTime);

        // include ordinal, e.g. 1st, 4th
        $startDayOfMonth = $startObj->DayOfMonth(true);

        $str = $startMonth . ' ' . $startDayOfMonth ;

        if (date(self::YMD_DATE_FORMAT, $startTime) == date(self::YMD_DATE_FORMAT, $endTime)) {
            //one date - str. has already been written
        } else {
            //two dates

            if ($endDateIsset) {
                $endMonth = date('M', $endTime);

                // include ordinal, e.g. 1st, 4th
                $endDayOfMonth = $endObj->DayOfMonth(true);

                if ($startMonth == $endMonth) {
                    $str .= ' - ' . $endDayOfMonth;
                } else {
                    $str .= ' - ' . $endMonth . ' ' . $endDayOfMonth;
                }
            }
        }
        return $str;
    }

    public static function formatted_start_date($startObj)
    {
        $startTime = strtotime($startObj->value);
        return date('M j, Y', $startTime);
    }

    public static function formatted_alldates($startObj, $endObj)
    {
        $startDate = date(self::YMD_DATE_FORMAT, strtotime($startObj->value));
        $endDate = date(self::YMD_DATE_FORMAT, strtotime($endObj->value));

        if ($startDate === $endDate) {
            return false;
        }

        $startTime = strtotime($startObj->value);
        $endTime = strtotime($endObj->value);

        // @todo This should be a separate helper method
        //
        // Note that the end date time is set when editing, this needs imported also
        if (date('g:ia', $startTime) == '12:00am') {
            $startDate = date('M j F, Y', $startTime);
        } else {
            $startDate = date('M j, Y (g:ia)', $startTime);
        }

        // @todo see note above
        // @tod null date passes this test without the addition of empty
        if (date('g:ia', $endTime) == '12:00am' && !empty($endDate)) {
            $endDate = date('M j, Y', $endTime);
        } else {
            // This is the straddling midnight case
            // time zones do not matter here, it is the delta in hours that is required
            $startDateCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $startObj->value);
            $endDateCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $endObj->value);

            $durationHrs = $endDateCarbon->diffInHours($startDateCarbon);
            $endDate = date('M j, Y', $endTime) . '  (' . $durationHrs . ' hrs)';
        }

        return $startDate." &ndash; ".$endDate;
    }

    /**
     * Formatted time frame
     * Returns either a string or null
     * Time frame is only applicable if both start and end time is on the same day
     *
     * @param  DBDatetime $startDBDateTime
     * @param  DBDatetime $endDBDateTime
     * @return string|null
     */
    public static function formatted_timeframe($startDBDateTime, $endDBDateTime)
    {
        $str = null;

        $startTime = strtotime($startDBDateTime->value);
        $endTime = strtotime($endDBDateTime->value);

        if ($startTime === $endTime) {
            return null;
        }

        error_log('START TIME: ' . $startTime);
        error_log('END TIME: ' . $endTime);
        error_log($startDBDateTime->value);
        error_log($endDBDateTime->value);


        if ($endTime) {
            //time frame is only applicable if both start and end time is on the same day
            if (date('Y-m-d', $startTime) == date('Y-m-d', $endTime)) {
                $str = date('g:ia', $startTime) . ' - ' . date('g:ia', $endTime);
            }
        } else {
            $str = date('g:ia', $startTime);
        }

        return $str;
    }
}
