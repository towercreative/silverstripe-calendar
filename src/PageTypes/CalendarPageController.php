<?php
namespace TitleDK\Calendar\PageTypes;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTP;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\Requirements;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarConfig;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Helpers\CalendarPageHelper;
use TitleDK\Calendar\Registrations\EventRegistration;
use TitleDK\Calendar\Tags\EventTag;

// @todo using page controller, the output for textIndex was likes of a default page controller
/**
 * Class \TitleDK\Calendar\PageTypes\CalendarPageController
 *
 * @property \TitleDK\Calendar\PageTypes\CalendarPage dataRecord
 * @method \TitleDK\Calendar\PageTypes\CalendarPage data()
 * @mixin \TitleDK\Calendar\PageTypes\CalendarPage
 */
class CalendarPageController extends ContentController
{
    use DateTimeHelperTrait;

    private static $allowed_actions = array(
        'past', // displaying past events
        'from', // displaying events from a specific date
        'detail', // details of a specific event
        'register', // event registration (only active if "registrations" is activated)
        'calendarview', // calendar view (only active if enabled under "pagetypes")
        'eventlist',
        'eventregistration',
        'search',
        'calendar',
        'registered',
        'noregistrations',
        'tag',

        // event listings contextual to current time
        'recent',
        'upcoming'
    );

    /** @var CalendarPageHelper */
    private $calendarPageHelper;

    private static $url_handlers = [
        '' => 'upcoming',
        'recent' => 'recent'
    ];


    public function init()
    {
        parent::init();
        Requirements::javascript('//code.jquery.com/jquery-3.3.1.min.js');
        Requirements::javascript('titledk/silverstripe-calendar:javascript/pagetypes/CalendarPage.js');
        Requirements::css('titledk/silverstripe-calendar:css/pagetypes/CalendarPage.css');
        Requirements::css('titledk/silverstripe-calendar:css/modules.css');
        $this->calendarPageHelper = new CalendarPageHelper();
    }

    /**
     * Coming events
     */
    public function index()
    {
        parent::index();

        // @todo config
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($indexSetting == 'eventlist') {

            // @todo What should be here?
            $events = $this->Events(); // already paged

            return [
                'Events' => $events
            ];
            return $this->returnTemplate();
        } elseif ($indexSetting == 'calendarview') {
            return $this->calendarview()->renderWith(['CalendarPage_calendarview', 'Page']);
        }
    }

    /**
     * Show upcoming events
     */
    public function upcoming()
    {
        $events = $this->UpComingEvents() ;

        return [
            'Events' => new PaginatedList($events, $this->getRequest())
        ];
    }

    /**
     * Show recent events
     *
     */
    public function recent()
    {
        $events = $this->RecentEvents();

        return [
            'Events' => new PaginatedList($events, $this->getRequest())
        ];
    }

    public function eventlist()
    {
        return $this->returnTemplate();
        // return $this;
    }

    public function registered($req)
    {
        //This has been taken out for now - should go to an own module
        //If you need this, contact Anselm (ac@title.dk)
    }
    public function eventregistration()
    {
        //TODO: filter this so only registerable events are shown
        return $this->returnTemplate();
        // return $this;
    }


    /**
     * Calendar View
     * Renders the fullcalendar
     */
    public function calendarview()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');

        if (isset($s['calendarpage']['calendarview']) && $s['calendarpage']['calendarview']) {
            $prefix = 'titledk/silverstripe-calendar:thirdparty/fullcalendar';
            Requirements::javascript($prefix . '/2.9.1/fullcalendar/lib/moment.min.js');
            Requirements::javascript($prefix . '/2.9.1/fullcalendar/fullcalendar.min.js');
            Requirements::css($prefix . '/2.9.1/fullcalendar/fullcalendar.min.css');
            Requirements::css($prefix . '/2.9.1/fullcalendar/fullcalendar.print.css', 'print');

            //xdate - needed for some custom code - e.g. shading
            Requirements::javascript('titledk/silverstripe-calendar:thirdparty/xdate/xdate.js');

            Requirements::javascript(
                'titledk/silverstripe-calendar:javascript/fullcalendar/PublicFullcalendarView.js'
            );

            $url = CalendarHelper::add_preview_params($this->Link(), $this->data());

            // @todo SS4 config
            $fullcalendarjs = $s['calendarpage']['fullcalendar_js_settings'];

            $controllerUrl = CalendarHelper::add_preview_params($s['calendarpage']['controllerUrl'], $this->data());

            //shaded events
            $shadedEvents = 'false';
            $sC = CalendarConfig::subpackage_settings('calendars');
            if ($sC['shading']) {
                $shadedEvents = 'true';
            }

            $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars(), true);

            //Calendar initialization (and possibility for later configuration options)
            Requirements::customScript(
                "
				(function($) {
					$(function () {
						//Initializing fullcalendar
						var cal = new PublicFullcalendarView($('#calendar'), '$url', {
							controllerUrl: '$controllerUrl',
							fullcalendar: {
								$fullcalendarjs
							},
							shadedevents: $shadedEvents,
							calendars: \"{$calendarIDs}\"
						});
					});
				})(jQuery);
			"
            );

            return $this;
        } else {
            return $this->httpError(404);
        }
    }


    /**
     * Displays details of an event
     *
     * @param  \HttpRequest $req
     * @return array
     */
    public function detail($req)
    {
        $session = $req->getSession();

        // @todo extension?
        $successfullyRegistered = $session->get(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);
        $session->clear(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);

        $registration = null;
        $registrationID = $session->get(EventRegistration::EVENT_REGISTRATION_KEY);
        if (!empty($registrationID)) {
            $registration = EventRegistration::get()->byID($registrationID);
        }

        $event = Event::get()->byID($req->param('ID'));
        if (!$event) {
            return $this->httpError(404);
        }
        return array(
            'Event'    => $event,
            'SuccessfullyRegistered' => $successfullyRegistered,
            'EventRegistration' => $registration
        );
    }


    /**
     * Display events for all tags - note no filtering currently
     *
     * @param  $req
     * @return array
     */
    public function tag($req)
    {
        $tagName = $req->param('ID');
        $tag = EventTag::get()->filter('URLSegment', $tagName)->first();
        $events = $tag->Events()->sort('StartDateTime DESC');

        $pagedEvents = new PaginatedList($events);
        $grid = $this->owner->createGridLayout($pagedEvents, 2);

        return [
            'Events' => $pagedEvents,
            'TagTitle' => $tag->Title,
            'GridLayout' => $grid
        ];
    }

    /**
     * Event registration
     *
     * @param  $req
     * @return array
     */
    public function register($req)
    {
        // @todo config a la SS4
        if (CalendarConfig::subpackage_enabled('registrations')) {
            return $this->detail($req);
        } else {
            return $this->httpError(404);
        }
    }

    /**
     * Returns true if registrations enabled
     *
     * @todo   Fix to SS4 config
     * @return bool are registrations enabled
     */
    public function RegistrationsEnabled()
    {
        return (bool) $this->config()->get('registrations_enabled');

    }

    public function SearchEnabled()
    {
        return (bool) $this->config()->get('search_enabled');
    }

    /**
     * Paginated event list for "eventlist" mode.  This will only show events for the current calendar page calendars,
     * and will also take account of calendars restricted by Group
     *
     * @param $paged true to paginate the list
     *
     * @return PaginatedList
     */
    public function Events()
    {
        $action = $this->request->param('Action');

        //Normal & Registerable events
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($action == 'eventregistration'
            || $action == 'eventlist'
            || ($action == '' && $indexSetting == 'eventlist')

        ) {
            $events = $this->getRegisterableEvents($action);
            return  new PaginatedList($events, $this->getRequest());
        }

        //Search
        if ($action == 'search') {
            $events = $this->performSearch();
            return new PaginatedList($events, $this->getRequest());
        }

    }


    private function getRegisterableEvents($action)
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());

        // This method takes a csv of IDs, not an array.
        $events = CalendarHelper::events_for_month($this->CurrentMonth(), $calendarIDs);

        if ($action == 'eventregistration') {
            $events = $events
                ->filter('Registerable', 1);
        }
        return $events;
    }

    private function performSearch()
    {
        $query = $this->SearchQuery();
        return $this->calendarPageHelpler()->performSearch($query);
    }


    private function RecentEvents()
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());

        $now = $this->RealtimeMonthDay();
        $prev = strtotime('-1 month', time());
        $oneMonthAgo = date('Y-m-d', $prev);

        // This method takes a csv of IDs, not an array.
        $events = CalendarHelper::events_for_date_range($oneMonthAgo, $now, $calendarIDs)
            ->sort('StartDateTime DESC');

        return  new PaginatedList($events, $this->getRequest());
    }

    private function UpComingEvents()
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());

        $currentMonth = $this->CurrentMonth();

        $now = $this->RealtimeMonthDay();


        $nowMonth = substr($now,0,7);

        // if nowMonth is the same as the current month, as in realtime month

        $start = null;
        $finish = null;

        if ($currentMonth == $nowMonth) {
            $start = $now;
        } else {
            $start = $currentMonth . '-01';
        }

        $startCarbon = $this->carbonDateTime($start .' 00:00:00')->timestamp;
        $next = strtotime('+1 month', $startCarbon);
        $inOneMonth = date('Y-m-d', $next);


        // This method takes a csv of IDs, not an array.
        $events = CalendarHelper::events_for_date_range($start, $inOneMonth, $calendarIDs)
            ->sort('"StartDateTime" ASC');

        return  new PaginatedList($events, $this->getRequest());
    }

    /**
     * Renders the current calendar, if a calenar link has been supplied via the url
     */
    public function CurrentCalendar()
    {
        $url = Convert::raw2url($this->request->param('ID'));

        $cal = Calendar::get()
            ->filter('URLSegment', $url)
            ->First();
        return $cal;
    }

    public function EventPageTitle()
    {
        $action = $this->getRequest()->param('Action');

        // @todo Do this smarter
        if (isset($_GET['month'])) {
            return $this->CurrentMonthStr();
        } elseif ($action == 'upcoming') {
            return 'Upcoming';
        } else {
            $action = 'Recent';
        }
    }


    // @todo this is badly named as it uses the parameterized month, not 'now'
    public function CurrentMonth()
    {
        if (isset($_GET['month'])) {
            return $_GET['month'];
        } else {
            $month = date('Y-m', time());
            return $month;
        }
    }

    public function RealtimeMonthDay()
    {
        $r =  date('Y-m-d', time());
        return $r;
    }

    public function RealtimeNextMonthDay()
    {
        $r =  date('Y-m-d', time());
        return $r;
    }

    // @todo This is inconsistent with the JavaScript which uses the full month name
    public function CurrentMonthStr()
    {
        $month = $this->CurrentMonth();
        $t = strtotime($month);
        $month = date('M Y', $t);

        return $month;
    }

    public function NextMonth()
    {
        $month = $this->CurrentMonth();
        $t = strtotime($month);
        $next = strtotime('+1 month', $t);
        $month = date('Y-m', $next);
        return $month;
    }

    public function NextMonthLink()
    {
        $month = $this->NextMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);
        return CalendarHelper::add_preview_params($url, $this->data());
    }

    public function PrevMonth()
    {
        $month = $this->CurrentMonth();
        $t = strtotime($month);
        $prev = strtotime('-1 month', $t);
        $month = date('Y-m', $prev);
        return $month;
    }

    public function PrevMonthLink()
    {
        $month = $this->PrevMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);
        return CalendarHelper::add_preview_params($url, $this->data());
    }


    public function EventListLink()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($indexSetting == 'eventlist') {
            return CalendarHelper::add_preview_params($this->Link(), $this->data());
        } elseif ($indexSetting == 'calendarview') {
            return CalendarHelper::add_preview_params($this->Link('eventlist'), $this->data());
        }
    }


    public function CalendarViewLink()
    {
        $s = CalendarConfig::subpackage_settings('pagetypes');
        $indexSetting = $s['calendarpage']['index'];
        if ($indexSetting == 'eventlist') {
            return CalendarHelper::add_preview_params($this->Link('calendarview'), $this->data());
        } elseif ($indexSetting == 'calendarview') {
            return CalendarHelper::add_preview_params($this->Link(), $this->data());
        }
    }

    public function SearchQuery()
    {
        // @todo SQL injection risk here I suspect
        if (isset($_GET['q'])) {
            $q = $_GET['q'];
            return $q;
        } else {
            return 'Search events';
        }
    }


    public function AllCalendars()
    {
        $calendars = Calendar::get();
        return $calendars;
    }

    public function FeedLink($calendarID)
    {
        $calendar = Calendar::get()->byID(intval($calendarID));
        $url = Controller::join_links($this->Link(), 'calendar', ($calendar) ? $calendar->Link : '');
        return CalendarHelper::add_preview_params($url, $this->data());
    }
}
