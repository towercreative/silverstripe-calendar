<?php declare(strict_types = 1);

namespace TitleDK\Calendar\PageTypes;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTP;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\Requirements;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelper;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\Helpers\CalendarPageHelper;
use TitleDK\Calendar\Registrations\EventRegistration;
use TitleDK\Calendar\Tags\EventTag;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
// @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint

/**
 * Class \TitleDK\Calendar\PageTypes\CalendarPageController
 *
 * @property \TitleDK\Calendar\PageTypes\CalendarPage dataRecord
 * @method \TitleDK\Calendar\PageTypes\CalendarPage data()
 * @mixin \TitleDK\Calendar\PageTypes\CalendarPage
 */
class CalendarPageController extends \PageController
{

    use DateTimeHelper;

    /** @var \TitleDK\Calendar\Helpers\CalendarPageHelper */
    private $calendarPageHelper;

    private static $allowed_actions = [
        'past',
        'from',
        'calendarview',
        'eventlist',
        'eventregistration',
        'search',
        'calendar',
        'registered',
        'noregistrations',
        'tag',
        'recent',
        'upcoming',
    ];

    private static $url_handlers = [
        '' => 'upcoming',
        'recent' => 'recent',
    ];


    public function init(): void
    {
        parent::init();

        Requirements::javascript('//code.jquery.com/jquery-3.3.1.min.js');
        Requirements::javascript('titledk/silverstripe-calendar:javascript/pagetypes/CalendarPage.js');
        Requirements::css('titledk/silverstripe-calendar:css/pagetypes/CalendarPage.css');
        Requirements::css('titledk/silverstripe-calendar:css/modules.css');
        $this->calendarPageHelper = new CalendarPageHelper();
    }


    /**
     * Show the upcoming events
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText|array<\SilverStripe\ORM\PaginatedList>
     */
    public function index()
    {
        parent::index();

        // @todo config
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
        if ($indexSetting === 'eventlist') {
            // @todo What should be here?
            // already paged
            $events = $this->Events();

            return [
                'Events' => $events,
            ];
        }

        if ($indexSetting === 'calendarview') {
            return $this->calendarview()->renderWith(['CalendarPage_calendarview', 'Page']);
        }
    }


    /**
     * Show upcoming events
     *
     * @return array<string, \SilverStripe\ORM\PaginatedList>
     */
    public function upcoming(): array
    {
        $events = $this->UpComingEvents();

        return [
            'Events' => new PaginatedList($events, $this->getRequest()),
        ];
    }


    /**
     * Show recent events
     *
     * @return array<string, \SilverStripe\ORM\PaginatedList>
     */
    public function recent(): array
    {
        $events = $this->RecentEvents();

        return [
            'Events' => new PaginatedList($events, $this->getRequest()),
        ];
    }


    /**
     * @return array<string,string>
     * @todo what is the correct phpdoc type here
     */
    public function eventlist(): array
    {
        // @todo This seems to fix the return template issue
        return [];
    }


    /**
     * @return array<string,string>
     * @todo what is the correct phpdoc type here
     */
    public function eventregistration(): array
    {
        // @todo This seems to fix the return template issue
        return [];
    }


    /**
     * Calendar View
     * Renders the fullcalendar
     *
     * @todo is this correct?
     */
    public function calendarview(): Calendar
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
                'titledk/silverstripe-calendar:javascript/fullcalendar/PublicFullcalendarView.js',
            );

            $url = CalendarHelper::addPreviewParams($this->Link(), $this->data());

            // @todo SS4 config
            $fullcalendarjs = Config::inst()->get(CalendarPage::class, 'fullcalendar_js_settings');
            $fullcalendarjs = \json_encode($fullcalendarjs);

            $configuredURL = Config::inst()->get(CalendarPage::class, 'controllerUrl');
            $controllerUrl = CalendarHelper::addPreviewParams($configuredURL, $this->data());

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
			",
            );

            return $this;
        }

        return $this->httpError(404);
    }


    /**
     * Displays details of an event
     *
     * @return array<mixed>
     */
    public function detail(): array
    {
        $session = $this->getRequest()->getSession();

        // @todo extension?
        $successfullyRegistered = $session->get(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);
        $session->clear(EventRegistration::EVENT_REGISTRATION_SUCCESS_SESSION_KEY);

        $registration = null;
        $registrationID = $session->get(EventRegistration::EVENT_REGISTRATION_KEY);
        if (isset($registrationID)) {
            $registration = EventRegistration::get()->byID($registrationID);
        }

        $event = Event::get()->byID($this->getRequest()->param('ID'));
        if (!$event) {
            return $this->httpError(404);
        }

        return [
            'Event' => $event,
            'SuccessfullyRegistered' => $successfullyRegistered,
            'EventRegistration' => $registration,
        ];
    }


    /**
     * Display events for all tags - note no filtering currently
     *
     * @return array<string,mixed>
     */
    public function tag(): array
    {
        $req = $this->getRequest();
        $tagName = $req->param('ID');
        $tag = EventTag::get()->filter('Slug', $tagName)->first();
        $events = $tag->Events()->sort('StartDateTime DESC');

        $pagedEvents = new PaginatedList($events);

        return [
            'Events' => $pagedEvents,
            'TagTitle' => $tag->Title,
        ];
    }


    /**
     * Event registration
     *
     * @return array<mixed>
     */
    public function register(): array
    {
        return Config::inst()->get(EventRegistration::class, 'enabled')
            ? $this->detail()
            : $this->httpError(404);
    }


    /**
     * Returns true if registrations enabled
     *
     * @return bool are registrations enabled
     */
    public function RegistrationsEnabled(): bool
    {
        return (bool)$this->config()->get('registrations_enabled');
    }


    /** @return bool template method to decide whether to render the search box or not */
    public function SearchEnabled(): bool
    {
        return (bool)$this->config()->get('search_enabled');
    }


    /**
     * Paginated event list for "eventlist" mode. This will only show events for the current calendar page calendars,
     * and will also take account of calendars restricted by Group
     *
     * @param $paged true to paginate the list
     */
    public function Events(): PaginatedList
    {
        $action = $this->request->param('Action');

        //Normal & Registerable events
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
        if ($action === 'eventregistration'
            || $action === 'eventlist'
            || ($action === '' && $indexSetting === 'eventlist')

        ) {
            $events = $this->getRegisterableEvents($action);

            return new PaginatedList($events, $this->getRequest());
        }

        //Search
        if ($action === 'search') {
            $events = $this->performSearch();

            return new PaginatedList($events, $this->getRequest());
        }
    }


    /**
     * Renders the current calendar, if a calenar link has been supplied via the url. It is used in CalendarDetails.ss
     */
    public function CurrentCalendar(): Calendar
    {
        $url = Convert::raw2url($this->request->param('ID'));

        return Calendar::get()
            ->filter('Slug', $url)
            ->First();
    }


    public function EventPageTitle(): string
    {
        $action = $this->getRequest()->param('Action');
        $month = $this->getRequest()->getVar('month');

        if (isset($month)) {
            return $this->calendarPageHelper->currentContextualMonthStr();
        }

        if ($action === 'upcoming') {
            return 'Upcoming';
        }

        return 'Recent';
    }


    // ---- link generators for templates ----

    public function NextMonthLink(): string
    {
        $month = $this->calendarPageHelper->nextContextualMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);

        return CalendarHelper::addPreviewParams($url, $this->data());
    }


    public function PrevMonthLink(): string
    {
        $month = $this->calendarPageHelper->previousContextualMonth();
        $url = $this->Link($this->request->param('Action'));
        $url = HTTP::setGetVar('month', $month, $url);

        return CalendarHelper::addPreviewParams($url, $this->data());
    }


    public function EventListLink(): string
    {
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
        if ($indexSetting === 'eventlist') {
            return CalendarHelper::addPreviewParams($this->Link(), $this->data());
        }

        if ($indexSetting === 'calendarview') {
            return CalendarHelper::addPreviewParams($this->Link('eventlist'), $this->data());
        }
    }


    /** @return string relevant link for the calendar given the index setting */
    public function CalendarViewLink(): string
    {
        $indexSetting = Config::inst()->get(CalendarPage::class, 'index');
        if ($indexSetting === 'eventlist') {
            return CalendarHelper::addPreviewParams($this->Link('calendarview'), $this->data());
        }

        if ($indexSetting === 'calendarview') {
            return CalendarHelper::addPreviewParams($this->Link(), $this->data());
        }
    }


    /** @return string link to feed */
    public function FeedLink(int $calendarID): string
    {
        $calendar = Calendar::get()->byID(\intval($calendarID));
        $url = Controller::join_links($this->Link(), 'calendar', ($calendar) ? $calendar->Link : '');

        return CalendarHelper::addPreviewParams($url, $this->data());
    }


    /**
     * @return string The search query string entered by the user, or 'Search events'. This is used in 2 templates,
     * EventSearch.ss and CalendarPageMenu.ss
     */
    public function SearchQuery(): string
    {
        /** @var string $query */
        $query = $this->getRequest()->getVar('q');
        // @todo SQL injection risk here I suspect
        if (isset($query)) {
            return $query;
        }

        // @TODO is this valid?
        return 'Search events';
    }


    /**
     * This is used in CalendarKeys.ss
     */
    public function AllCalendars(): \SilverStripe\ORM\DataList
    {
        return Calendar::get();
    }


    private function getRegisterableEvents(string $action): \SilverStripe\ORM\DataList
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());

        // This method takes a csv of IDs, not an array.
        $events = CalendarHelper::eventsForMonth($this->calendarPageHelper->currentContextualMonth(), $calendarIDs);

        if ($action === 'eventregistration') {
            $events = $events
                ->filter('Registerable', 1);
        }

        return $events;
    }


    private function performSearch(): \SilverStripe\ORM\DataList
    {
        $query = $this->SearchQuery();

        return $this->calendarPageHelper->performSearch($query);
    }


    /** @throws \Exception */
    private function RecentEvents(): PaginatedList
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());
        $events = $this->calendarPageHelper->recentEvents($calendarIDs);

        return new PaginatedList($events, $this->getRequest());
    }


    /** @throws \Exception */
    private function UpComingEvents(): PaginatedList
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->Calendars());
        $events = $this->calendarPageHelper->upcomingEvents($calendarIDs);

        return new PaginatedList($events, $this->getRequest());
    }
}
