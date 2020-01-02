<?php
namespace TitleDK\Calendar\Helpers;

use Carbon\Carbon;
use TitleDK\Calendar\Core\CalendarHelper;


class CalendarPageHelper
{
    public function realtimeMonthDay()
    {
        return date('Y-m-d', Carbon::now()->timestamp);
    }

    public function realtimeMonth()
    {
        return date('Y-m', Carbon::now()->timestamp);
    }

    /**
     * If a month paramater is set, such as 2020-01 use that, otherwise use Carbon::now(), the current time, as the
     * basis for forming an equivalent string
     * @return string
     */
    public function currentContextualMonth()
    {
        if (isset($_GET['month'])) {
            return $_GET['month'];
        } else {
            return $this->realtimeMonth();
        }
    }


    // @todo This is inconsistent with the JavaScript which uses the full month name
    public function currentContextualMonthStr()
    {
        $month = $this->currentContextualMonth();
        $t = strtotime($month);
        return date('M Y', $t);
    }

    public function previousContextualMonth()
    {
        $month = $this->currentContextualMonth();
        $t = strtotime($month);
        $prev = strtotime('-1 month', $t);
        return date('Y-m', $prev);
    }

    public function nextContextualMonth()
    {
        $month = $this->currentContextualMonth();
        $t = strtotime($month);
        $next = strtotime('+1 month', $t);
        return date('Y-m', $next);
    }




    public function performSearch($query)
    {
        // @todo This is case sensitive with Postgresql, not so with MySQL
        //$query = strlower(addslashes($query));
        $query = (addslashes($query));
        //Debug::dump($query);
        $qarr = preg_split('/[ +]/', $query);

        $filter = '';
        $first = true;
        foreach ($qarr as $qitem) {
            if (!$first) {
                $filter .= " AND ";
            }

            $filter .= " (
					\"Title\" LIKE '%$qitem%'
					OR \"Details\" LIKE '%$qitem%'
				)";
            $first = false;
        }


        //Debug::dump($filter);
        // @todo this is incorrect, does not take into context of current calendars
        $events = CalendarHelper::all_events()
            ->where($filter);
        return $events;
    }
}
