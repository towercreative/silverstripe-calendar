<?php
namespace TitleDK\Calendar\Helpers;

use TitleDK\Calendar\Core\CalendarHelper;


class CalendarPageHelper
{
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
        $events = CalendarHelper::all_events()
            ->where($filter);
        return $events;
    }
}
