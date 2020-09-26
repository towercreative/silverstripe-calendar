<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Controllers;

use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Utility\Formatter;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Helpers\ICSExportHelper;

//by Anselm

/**
 * ICS Export controller
 *
 * Usage:
 * * "all" link is "/ics/all", like this: "/ics/all?dump=1" for debugging.
 * * Example links (will only work once you've called the get methods at least once):
 *   * Excursion calendar: /ics/cal/excursions
 *
 * More explaination:
 * https://github.com/colinburns/daypack.com.au/issues/48
 */
class ICSExportController extends Controller
{

    private static $allowed_actions = [
        'cal',
        'all',
        'my',
    ];

    public function init(): void
    {
        parent::init();
    }


    public function index(): bool
    {
        return false;
    }


    /**
     * Single calendar
     * Can be public or private
     * For public calendars either the calendar id or the calendar url can be supplied
     * For private calendars user email and hash need to be supplied - like this.
     * The private calendar hash is created in {@see PrivateCalendarMemberExtension}
     *
     * @return string calendar in ICS format
     */
    public function cal(): string
    {
        $cal = null;
        $request = $this->getRequest();

        $ics = null;

        $idOrURL = $request->param('ID');

        //Public calendar via id
        if (\is_numeric($idOrURL)) {
            //calendar id is requested
            $cal = Calendar::get()->ByID((int)$request->param('ID'));

            //Public calendar via url
        } else {
            //calendar url is requested
            $url = Convert::raw2url($idOrURL);

            $cal = Calendar::get()
                ->filter('Slug', $url)
                ->First();
        }

        //If not public calendar is found we check for a private calendar
        if (!$cal) {
            echo $idOrURL;
            $member = Member::get()
                ->filter(Email::class, $idOrURL)
                ->filter('PrivateCalendarKey', $request->param('OtherID'))
                ->First();
            if ($member && $member->exists()) {
                return $this->memberCalendar($member);
            }
        }


        if ($cal && $cal->exists()) {
            //everybody can access public calendars
            if ($cal->ClassName === 'Calendar') {
                $helper = new ICSExportHelper();
                $ics = $helper->processCalendar($cal);
                $calName = $cal->Title;
            }

            return $this->output($ics, $calName);
        } else {
            echo "calendar can't be found";
        }
    }


    /**
     * All public calendars
     */
    public function all(): string
    {
        $calendars = Calendar::get();
        $events = new ArrayList();
        foreach ($calendars as $cal) {
            $events->merge($cal->Events());
        }

        $eventsArr = $events->toNestedArray();

        $ics = $this->exportICS($eventsArr);

        return $this->output($ics, 'all');
    }


    /**
     * The currently logged in user's calendar
     */
    public function my(): void
    {
        $member = Member::currentUser();
        if (!$member) {
            // @todo what to render here
            //return 'please log in';
        }

        $this->memberCalendar($member);
    }


    /** @return */
    protected function memberCalendar(Member $member): void
    {
        $events = PrivateEvent::get()
            ->filter(
                [
                    'OwnerID' => $member->ID,
                ]
            )
            ->filter(
                [
                    'StartDateTime:GreaterThan' => PrivateCalendarController::offset_date('start', null, 300),
                    'EndDateTime:LessThan' => PrivateCalendarController::offset_date('end', null, 300),
                ]
            );

        $eventsArr = $events->toNestedArray();

        $ics = $this->exportICS($eventsArr);

        $this->output($ics, \strtolower($member->FirstName));
    }


    /**
     * @param array<\TitleDK\Calendar\Events\Event> $eventsArr
     * @return string The ICS for these events
     */
    private function exportICS(array $eventsArr): string
    {
        $icsCalendar = new Calendar();

        // @TODO Need sensible non fixed values here
        $icsCalendar->setProdId('-//My Company//Cool Calendar App//EN');

        /** @var \TitleDK\Calendar\Events\Event $ssEvent */
        foreach ($eventsArr as $ssEvent) {
            $icsEvent = new CalendarEvent();
            // @TODO add attendees, location, test this
            $icsEvent->setStart($ssEvent->start)
                ->setUid($ssEvent->ID)
                ->setUid($ssEvent->AllDay)
                ->setSummary($ssEvent->Title)
                ->setDescription($ssEvent->Details);
        }
        //$calendarExport = new CalendarExport(new CalendarStream, new Formatter());
        $calendarExport = new CalendarExport(new CalendarStream(), new Formatter());

        return $calendarExport->getStream();
    }


    // @TODO Include context param
    private function output(string $ics): void
    {
        // @TODO test this, not sure how it will work in the context of a browser, or indeed how it is meant to
        echo $ics;
    }
}
