<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 20/12/18
 * Time: 22:19
 */

namespace TitleDK\Calendar\Tasks;

use SilverStripe\Dev\BuildTask;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\CalendarPage;

class CreateExampleCalendarTask extends BuildTask
{
    /**
     * {@inheritDoc}
     * @var string
     */
    private static $segment = 'CreateExampleCalendar';

    protected $title = 'Create an example calendar with events';

    /**
     * @throws Exception
     * @param HTTPRequest $request
     */
    public function run($request)
    {
        $c = new Calendar();
        $c->Title = 'Example';
        $c->write();

        $event = new Event();


        $cp = new CalendarPage();
        $cp->Title = 'Example Calendar Page';
        $cp->ParentID = 0;
        $cp->write();

        $event = new Event();
        $event->Title = 'Example Event';
    }
}
