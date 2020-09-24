<?php
namespace TitleDK\Calendar\PageTypes;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTP;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\Requirements;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Helpers\CalendarPageHelper;
use TitleDK\Calendar\Registrations\EventRegistration;
use TitleDK\Calendar\Tags\EventTag;

/**
 * Class \TitleDK\Calendar\PageTypes\CalendarPageController
 *
 * @property \TitleDK\Calendar\PageTypes\CalendarPage dataRecord
 * @method \TitleDK\Calendar\PageTypes\CalendarPage data()
 * @mixin \TitleDK\Calendar\PageTypes\CalendarPage
 */
class CalendarPageController extends \PageController
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
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
        if ($indexSetting == 'eventlist') {

            // @todo What should be here?
            $events = $this->Events(); // already paged

            return [
                'Events' => $events
            ];
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
        // @todo This seems to fix the return template issue
        return [];
    }


    public function eventregistration()
    {
        // @todo This seems to fix the return template issue
        return [];
    }


    /**
     * Calendar View
     * Renders the fullcalendar
     */
    public function calendarview()
    {
        if (Config::inst()->get(CalendarPage::class, 'calendarview')) {
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
            $fullcalendarjs = Config::inst()->get(CalendarPage::class, 'fullcalendar_js_settings');
            $fullcalendarjs = json_encode($fullcalendarjs);

            $configuredURL = Config::inst()->get(CalendarPage::class, 'controllerUrl');
            $controllerUrl = CalendarHelper::add_preview_params($configuredURL, $this->data());

            $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars(), true);

            //Calendar initialization (and possibility for later configuration options)
            Requirements::customScript(
                "
				(function($) {
					$(function () {
						//Initializing fullcalendar
						var cal = new PublicFullcalendarView($('#calendar'), '$url', {
							controllerUrl: \"$controllerUrl\",
							fullcalendar:$fullcalendarjs,
							shadedevents: false,
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
     * @return array
     */
    public function detail()
    {
        $session = $this->getRequest()->getSession();

        // @todo extension?
        $successfullyRegistered = $session->get(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);
        $session->clear(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);

        $registration = null;
        $registrationID = $session->get(EventRegistration::EVENT_REGISTRATION_KEY);
        if (!empty($registrationID)) {
            $registration = EventRegistration::get()->byID($registrationID);
        }

        $event = Event::get()->byID($this->getRequest()->param('ID'));
        if (!$event) {
            return $this->httpError(404);
        }
        return [
            'Event'    => $event,
            'SuccessfullyRegistered' => $successfullyRegistered,
            'EventRegistration' => $registration
        ];
    }


    /**
     * Display events for all tags - note no filtering currently
     *
     * @return array
     */
    public function tag()
    {
        $req = $this->getRequest();
        $tagName = $req->param('ID');
        $tag = EventTag::get()->filter('Slug', $tagName)->first();
        $events = $tag->Events()->sort('StartDateTime DESC');

        $pagedEvents = new PaginatedList($events);

        return [
            'Events' => $pagedEvents,
            'TagTitle' => $tag->Title
        ];
    }

    /**
     * Event registration
     *
     * @return array
     */
    public function register()
    {
        if (Config::inst()->get(EventRegistration::class, 'enabled')) {
            return $this->detail();
        } else {
            return $this->httpError(404);
        }
    }

    /**
     * Returns true if registrations enabled
     *
     * @return bool are registrations enabled
     */
    public function RegistrationsEnabled()
    {
        return (bool) $this->config()->get('registrations_enabled');

    }

    /**
     * @return bool template method to decide whether to render the search box or not
     */
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
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
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
        $events = CalendarHelper::events_for_month($this->calendarPageHelper->currentContextualMonth(), $calendarIDs);

        if ($action == 'eventregistration') {
            $events = $events
                ->filter('Registerable', 1);
        }
        return $events;
    }


    private function performSearch()
    {
        $query = $this->SearchQuery();
        return $this->calendarPageHelper->performSearch($query);
    }


    private function RecentEvents()
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());
        $events = $this->calendarPageHelper->recentEvents($calendarIDs);
        return  new PaginatedList($events, $this->getRequest());
    }

    private function UpComingEvents()
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());
        $events = $this->calendarPageHelper->upcomingEvents($calendarIDs);
        return  new PaginatedList($events, $this->getRequest());
    }

    /**
     * Renders the current calendar, if a calenar link has been supplied via the url.  It is used in CalendarDetails.ss
     */
    public function CurrentCalendar()
    {
        $url = Convert::raw2url($this->request->param('ID'));

        $cal = Calendar::get()
            ->filter('Slug', $url)
            ->First();
        return $cal;
    }

    public function EventPageTitle()
    {
        $action = $this->getRequest()->param('Action');

        // @todo Do this smarter
        if (isset($_GET['month'])) {
            return $this->calendarPageHelper->currentContextualMonthStr();
        } elseif ($action == 'upcoming') {
            return 'Upcoming';
        } else {
            return 'Recent';
        }
    }


    // ---- link generators for templates ----
    public function NextMonthLink()
    {
        $month = $this->calendarPageHelper->nextContextualMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);
        return CalendarHelper::add_preview_params($url, $this->data());
    }

    public function PrevMonthLink()
    {
        $month = $this->calendarPageHelper->previousContextualMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);
        return CalendarHelper::add_preview_params($url, $this->data());
    }


    public function EventListLink()
    {
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
        if ($indexSetting == 'eventlist') {
            return CalendarHelper::add_preview_params($this->Link(), $this->data());
        } elseif ($indexSetting == 'calendarview') {
            return CalendarHelper::add_preview_params($this->Link('eventlist'), $this->data());
        }
    }


    public function CalendarViewLink()
    {
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
        if ($indexSetting == 'eventlist') {
            return CalendarHelper::add_preview_params($this->Link('calendarview'), $this->data());
        } elseif ($indexSetting == 'calendarview') {
            return CalendarHelper::add_preview_params($this->Link(), $this->data());
        }
    }


    public function FeedLink($calendarID)
    {
        $calendar = Calendar::get()->byID(intval($calendarID));
        $url = Controller::join_links($this->Link(), 'calendar', ($calendar) ? $calendar->Link : '');
        return CalendarHelper::add_preview_params($url, $this->data());
    }


    /**
     * @return string The search query string entered by the user, or 'Search events'.  This is used in 2 templates,
     * EventSearch.ss and CalendarPageMenu.ss
     */
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


    /**
     * This is used in CalendarKeys.ss
     *
     * @return \SilverStripe\ORM\DataList
     */
    public function AllCalendars()
    {
        $calendars = Calendar::get();
        return $calendars;
    }
}
