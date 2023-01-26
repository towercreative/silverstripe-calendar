<?php declare(strict_types=1);

namespace TitleDK\Calendar\Events;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
// @phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

use Carbon\Carbon;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TimeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\TagField\TagField;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelper;
use TitleDK\Calendar\PageTypes\CalendarPage;
use TitleDK\Calendar\PageTypes\EventPage;
use TitleDK\Calendar\Registrations\Helper\EventRegistrationTicketsHelper;
use TitleDK\Calendar\Tags\EventTag;

/**
 * Event Model
 *
 * Events must be associated with a calendar
 *
 * @package calendar
 * @property string $RegistrationEmbargoAt
 * @property bool $Registerable
 * @property string $Cost
 * @property bool $TicketsRequired
 * @property int $NumberOfAvailableTickets
 * @property bool $PaymentRequired
 * @property string $RSVPEmail
 * @property string $LocationName
 * @property string $MapURL
 * @property string $Title
 * @property bool $AllDay
 * @property bool $NoEnd
 * @property \SilverStripe\ORM\FieldType\DBDatetime $StartDateTime
 * @property string $TimeFrameType
 * @property string $Duration
 * @property \SilverStripe\ORM\FieldType\DBDatetime $EndDateTime
 * @property string $Details
 * @property int $FeaturedImageID
 * @property int $EventPageID
 * @property int $CalendarID
 * @method \SilverStripe\Assets\Image FeaturedImage()
 * @method \TitleDK\Calendar\PageTypes\EventPage EventPage()
 * @method \TitleDK\Calendar\Calendars\Calendar Calendar()
 * @method \SilverStripe\ORM\DataList|array<\TitleDK\Calendar\Registrations\EventRegistration> Registrations()
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\Categories\EventCategory> Categories()
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\Tags\EventTag> Tags()
 * @mixin \TitleDK\Calendar\Events\EventImageExtension
 * @mixin \TitleDK\Calendar\Events\EventLocationExtension
 * @mixin \TitleDK\Calendar\Registrations\EventRegistrationExtension
 * @mixin \TitleDK\Calendar\Categories\EventCategoryExtension
 * @mixin \TitleDK\Calendar\Events\EventRegistrationEmbargoExtension
 */
class Event extends DataObject
{

    use DateTimeHelper;


    protected $hasWritten = false;

    private static $table_name = 'Event';

    //Public events are simply called 'Event'
    private static $singular_name = 'Event';
    private static $plural_name = 'Events';

    private static $has_one = [
        'EventPage' => EventPage::class,
        'Calendar' => Calendar::class,
    ];

    private static $belongs_many_many = [
        'Tags' => EventTag::class,
    ];

    private static $db = [
        'Title' => 'Varchar(200)',
        'AllDay' => DBBoolean::class,
        'NoEnd' => DBBoolean::class,
        'StartDateTime' => DBDatetime::class,
        'TimeFrameType' => "Enum('Duration,DateTime','Duration')",
        'Duration' => 'Time',
        'EndDateTime' => DBDatetime::class,
        'Details' => 'HTMLText',
    ];

    /** @var array<string,string> */
    private static $summary_fields = [
        'Title' => 'Title',
        'StartDateTime' => 'Date and Time',
        'DatesAndTimeframe' => 'Presentation String',
        'TimeFrameType' => 'Time Frame Type',
        'Duration' => 'Duration',
        'Calendar.Title' => 'Calendar',
    ];

    /** @var string */
    private static $default_sort = 'StartDateTime';

    /**
     * @return array
     */
    public function summaryFields(): array
    {
        $fields = parent::summaryFields();

        // Add Calendar Title field if calendars are enabled
        $fields['Calendar.Title'] = 'Calendar';

        return $fields;
    }
    //Countering problem with onbefore write being called more than once
//See http://www.silverstripe.org/data-model-questions/show/6805


    /* ---- from event has event page extension ---- */

    //@todo this method is suspicous

    public function getEventPageCalendarTitle(): string
    {
        $owner = $this->owner;

        return $owner->EventPage()->exists()
            ? $owner->EventPage()->getCalendarTitle()
            : '-';
    }


    public function DetailsSummary():? string
    {
        if (!$this->Details) {
            return null;
        }

        return \implode(' ', \array_slice(\explode(
            ' ',
            \strip_tags($this->Details, "<a>"),
        ), 0, 25));
    }


    /**
     * Sanity checks before write
     * Rules for event saving:
     * 1. Events have
     */
    public function onBeforeWrite(): void
    {
        parent::onBeforeWrite();

        //only allowing to run this once:
        if ($this->hasWritten) {
            return;
        }
        $this->hasWritten = true;
        //echo "this should only execute once \n";


        //Convert to allday event if the entered time is 00:00
        //(i.e. this field has been left blank)
        //This only happens if allday events are enabled
        //NOTE: Currently it seems to me as if there should be no need to disable allday events
        if ($this->config()->get('enable_allday_events')) {
            //This only happens on first save to correct for the rare cases that someone might
            //actually want to add an event like this
            if (!$this->ID) {
                if (\date("H:i", \strtotime($this->StartDateTime)) === '00:00') {
                    $this->AllDay = true;
                }
            }
        }

        //If the timeframetype is duration - set end date based on duration
        if ($this->TimeFrameType === 'Duration') {
            $formatDate = $this->calcEndDateTimeBasedOnDuration();
            //only write the end date if a duration has actually been entered
            //If not, leave the end date blank for now, and it'll be taken care later in this method
            //setting the end date/time to null, as it has automatically been set via javascript
            $this->getFormattedStartDate() !== $formatDate
                ? $this->EndDateTime = $formatDate
                :
                $this->EndDateTime = null;
        } else {
            //reset duration
            $this->Duration = '';
        }

        //Sanity checks:

        //1. We always need an end date/time - if no end date is set, set end date 1 hour after start date
        //This won't happen if leaving end date/time empty is allowed through the config
        //This should not happen to single day allday events as these are supposed to have start and end date
        //set to the same date via the js in the edit form
        if ($this->config()->get('force_end')) {
            if (!$this->EndDateTime) {
                $this->EndDateTime = \date("Y-m-d H:i:s", \strtotime($this->StartDateTime) + 3600);
            }
        }

        //2. We can't have negative dates
        //If this happens for some reason, we make the event an allday event, and set start date = end date
        //Should only be triggered, if EndDateTime is set

        if (isset($this->EndDateTime)) {
            if (\strtotime($this->EndDateTime) < \strtotime($this->StartDateTime)) {
                $this->EndDateTime = $this->StartDateTime;
                $this->AllDay = true;
            }
        }

        //3. If end dates are not enforced, and no end date has been set, set the NoEnd attribute
        //Equally, if the Noend attribute has been set  via a checkbox, we reset EndDateTime and Duration
        if (!$this->config()->get('force_end')) {
            if (isset($this->EndDateTime)) {
                if ($this->NoEnd) {
                    $this->Duration = null;
                    $this->EndDateTime = null;
                }
            } else {
                $this->NoEnd = true;
            }
        }

        //4. All day events can't have open ends
        //so if and event both has the allday attribute and the noend attribute,
        //noend is enforced over allday
        if (!$this->AllDay || !$this->NoEnd) {
            return;
        }

        $this->AllDay = false;
    }


    /**
     * Set new start/end dates
     *
     * @param string $start Should be SS_Datetime compatible
     * @param string $end Should be SS_Datetime compatible
     * @param bool $write If true, write to the db
     */
    public function setStartEnd(string $start, string $end, bool $write = true): void
    {
        $e = $this;

        $e->StartDateTime = $start;
        $e->setEnd($end, false);
        if (!$write) {
            return;
        }

        $e->write();
    }


    /**
     * Set new end date
     *
     * @param string $end Should be SS_Datetime compatible
     * @param bool $write If true, write to the db
     */
    public function setEnd(string $end, bool $write = true): void
    {
        $e = $this;

        if ($e->TimeFrameType === 'DateTime') {
            $e->EndDateTime = $end;
        } elseif ($e->TimeFrameType === 'Duration') {
            $duration = $this->calcDurationBasedOnEndDateTime($end);
            if ($duration) {
                $e->Duration = $duration;
            } else {
                //if duration is more than 1 day, make the time frame "DateTime"
                $e->TimeFrameType = 'DateTime';
                $e->EndDateTime = $end;
            }
        }

        if (!$write) {
            return;
        }

        $e->write();
    }


    /**
     * Calculation of end date based on duration
     * Should only be used in OnBeforeWrite
     */
    public function calcEndDateTimeBasedOnDuration(): string
    {
        $duration = $this->Duration;

        $secs = ((int) \substr($duration, 0, 2) * 3600) +
            ((int) \substr($duration, 3, 2) * 60) +
            ((int) \substr($duration, 6, 2));

        $startDate = \strtotime($this->StartDateTime);

        $endDate = $startDate + $secs;

        return \date("Y-m-d H:i:s", $endDate);
    }


    /**
     * Calculation of duration based on end datetime
     * Returns false if there's more than 24h between start and end date
     *
     * @return string|false
     */
    public function calcDurationBasedOnEndDateTime(string $end)
    {
        $startDate = \strtotime($this->StartDateTime);
        $endDate = \strtotime($end);

        $duration = $endDate - $startDate;
        $secsInDay = 60 * 60 * 24;
        if ($duration > $secsInDay) {
            //Duration cannot be more than 24h
            return false;
        }

        //info on this calculation here:
        //http://stackoverflow.com/questions/3856293/how-to-convert-seconds-to-time-format
        return \gmdate("H:i", $duration);
    }


    /**
     * All Day getter
     * Any events that spans more than 24h will be displayed as allday events
     * Beyond that those events marked as all day events will also be displayed as such
     */
    public function isAllDay(): ?bool
    {
        if ($this->AllDay) {
            return true;
        }

        $secsInDay = 60 * 60 * 24;
        $startTime = \strtotime($this->StartDateTime);
        $endTime = \strtotime($this->EndDateTime);

        $durationInSeconds = $endTime - $startTime;

        return $durationInSeconds > $secsInDay;
    }


    /**
     * Frontend fields
     * Simple list of the basic fields - how they're intended to be edited
     *
     * @param $params
     * @return FieldList
     */
    public function getFrontEndFields($params = null): FieldList
    {
        $timeFrameHeaderText = 'Time Frame';
        if (!$this->config()->get('force_end')) {
            $timeFrameHeaderText = 'End Date / Time (optional)';
        }

        /** @var \SilverStripe\Forms\DatetimeField $startDateTime */
        $startDateTime = DatetimeField::create('StartDateTime', 'Start');

        /** @var \SilverStripe\Forms\DatetimeField $endDateTime */
        $endDateTime = DatetimeField::create('EndDateTime', '');

        $fields = FieldList::create(
            TextField::create('Title')
                ->setAttribute('placeholder', 'Enter a title'),
            CheckboxField::create('AllDay', 'All-day'),
            $startDateTime,
            //NoEnd field - will only be shown if end dates are not enforced - see below
            CheckboxField::create('NoEnd', 'Open End'),
            HeaderField::create('TimeFrameHeader', $timeFrameHeaderText, 5),
            SelectionGroup::create(
                'TimeFrameType',
                [
                    "Duration//Duration" => TimeField::create('Duration', '')->setRightTitle('up to 24h')
                        ->setAttribute('placeholder', 'Enter duration'),
                    "DateTime//Date/Time" => $endDateTime = DatetimeField::create('EndDateTime', ''),
                ],
            ),
            LiteralField::create('Clear', '<div class="clear"></div>'),
        );

        //@todo API for show calendar has changed
        $startDateTime
            //->getDateField()
            //->setConfig('showcalendar', 1)
            //->setRightTitle('Date')
            ->setAttribute('placeholder', 'Enter date')
            //we only want input through the datepicker
            ->setAttribute('readonly', 'true');
        $startDateTime
            //->getTimeField()
            //->setRightTitle($timeExpl)
            //->setConfig('timeformat', 'h:mm') //this is the default, that seems to be giving some troubles: h:mm:ss a
            //->setConfig('timeformat', 'HH:mm') //24h format
            ->setAttribute('placeholder', 'Enter time');

        $endDateTime
            //->getDateField()
            //->setConfig('showcalendar', 1)
            //->setRightTitle('Date')
            ->setAttribute('placeholder', 'Enter date')
            //we only want input through the datepicker
            ->setAttribute('readonly', 'true');
        $endDateTime
            //->getTimeField()
            //->setRightTitle($timeExpl)
            //->setConfig('timeformat', 'HH:mm') //24h fromat
            ->setAttribute('placeholder', 'Enter time');

        //removing AllDay checkbox if allday events are disabled
        if (!$this->config()->get('enable_allday_events')) {
            $fields->removeByName('AllDay');
        }
        //removing NoEnd checkbox if end dates are enforced
        if ($this->config()->get('force_end')) {
            $fields->removeByName('NoEnd');
        } else {
            //we don't want the NoEnd checkbox when creating new events
            if (!$this->ID) {
                //$fields->removeByName('NoEnd');
            }
        }


        $this->extend('updateFrontEndFields', $fields);

        return $fields;
    }


    /**
     * CMS Fields
     */
    public function getCMSFields(): FieldList
    {
        $eventFields = $this->getFrontEndFields();

        $fields = new FieldList();
        $fields->push(new TabSet("Root", $mainTab = new Tab('Main')));

        $fields->addFieldsToTab('Root.Main', $eventFields);

        //moving all day further down for CMS fields
        $allDay = $fields->dataFieldByName('AllDay');
        //$fields->removeByName('AllDay');
        $fields->addFieldToTab(
            'Root.Main',
            $allDay,
            'TimeFrameHeader',
        );

        $fields->addFieldToTab('Root.Details', $details = HTMLEditorField::create('Details', ''));
        $details->addExtraClass('stacked');

        $fields->addFieldToTab(
            'Root.RelatedPage',
            DropdownField::create(
                'EventPageID',
                'EventPage',
                EventPage::get()->sort('Title')->map('ID', 'Title'),
            )
                ->setEmptyString('Choose event page...'),
        );

        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'CalendarID',
                'Calendar',
                Calendar::get()->sort('Title')->map('ID', 'Title'),
            )
                ->setEmptyString('Choose calendar...'),
        );

        $tagField = TagField::create(
            'Tags',
            \_t(self::class . '.Tags', 'Tags'),
            EventTag::get(),
            $this->Tags(),
        )
            ->setCanCreate($this->canCreateTags())
            ->setShouldLazyLoad(true);

        $fields->addFieldToTab('Root.Main', $tagField);

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }


    public function getCMSValidator(): RequiredFields
    {
        return new RequiredFields(
            [
                'Title', 'CalendarID',
            ],
        );
    }


    public function getAddNewFields(): FieldList
    {
        return $this->getFrontEndFields();
    }


    // @todo Unit test
    public function getIsPastEvent(): bool
    {
        return \strtotime($this->StartDateTime) < \mktime(0, 0, 0, (int)\date('m'), (int)\date('d'), (int)\date('Y'));
    }


    /**
     * Template rendering
     *
     * @return \Carbon\Carbon|\SilverStripe\Forms\DatetimeField
     */
    public function RegistrationEmbargoDate()
    {
        return $this->getRegistrationEmbargoDate(true);
    }


    /**
     * Get the registration embargo date
     *
     * @return \Carbon\Carbon|\SilverStripe\Forms\DatetimeField the embargo time as a carbon date object
     */
    public function getRegistrationEmbargoDate(bool $returnAsDateTime = false)
    {
        $result = null;
        $embargo = $this->RegistrationEmbargoAt;

        if (!$embargo) {
            $mins = $this->config()->get('embargo_registration_relative_to_end_datetime_mins');

            // @todo Fix bug
            $result = $this->carbonDateTime($this->StartDateTime)->addMinutes($mins);
        } else {
            $result = $this->carbonDateTime($this->RegistrationEmbargoAt);
        }

        if ($returnAsDateTime) {
            $result = $this->getSSDateTimeFromCarbon($result);
        }

        return $result;
    }


    public function getIsPastRegistrationClosing(): bool
    {
        $expiryDate = $this->getRegistrationEmbargoDate();

        return $expiryDate->lte(Carbon::now());
    }


    /** @return false|string */
    public function getFormattedStartDate()
    {
        return EventHelper::formattedStartDate($this->obj('StartDateTime'));
    }


    /**
     * Formatted Dates
     * Returns either the event's date or both start and end date if the event spans more than
     * one date
     */
    public function getFormattedDates(): string
    {
        // @todo use standard silverstripe date formatters, otherwise we are looking at 2 different formatting types :(
        return EventHelper::formattedDates($this->obj('StartDateTime'), $this->obj('EndDateTime'));
    }


    public function getFormattedTimeframe(): ?string
    {
        return EventHelper::formattedTimeframe($this->dbObject('StartDateTime'), $this->dbObject('EndDateTime'));
    }


    /**
     * Render this with $StartAndEndDates.RAW
     *
     * @return bool|string
     */
    public function getStartAndEndDates()
    {
        return EventHelper::formattedAllDates($this->dbObject('StartDateTime'), $this->dbObject('EndDateTime'));
    }


    public function getDatesAndTimeframe(): string
    {
        $dates = $this->getFormattedDates();
        $timeframe = $this->getFormattedTimeframe();

        return $timeframe
            ? "$dates @ $timeframe"
            : $dates;
    }


    /**
     * Getter for internal event link
     * **** NOTE: The current implementation only works properly as long as there's only one
     * {@see CalendarPage} in the site ****
     */
    public function getInternalLink(): string
    {
        //for now all event details will only have one link - that is the main calendar page
        //NOTE: this could be amended by calling that link via AJAX, and thus could be shown as an overlay
        //everywhere on the site
        $calendarPage = CalendarPage::get()->First();

        return CalendarHelper::addPreviewParams(
            Controller::join_links($calendarPage->Link('detail'), $this->ID),
            $this
        );
    }


    /**
     * Get a link relative to the current calendar page URL. This is for rendering in calendar page event listings
     */
    public function getRelativeLink(): string
    {
        return 'detail/' . $this->ID;
    }


    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @param $member
     * @param $context
     * @return bool
     */
    public function canCreate($member = null, $context = [])
    {
        return $this->canManage($member);
    }


    /*
    **
    * Determine whether user can create new tags.
    *
    * @param null|int|Member $member
    *
    * @return bool
    */
    public function canCreateTags(Member $member = null): bool
    {
        return $this->canManage($member);
    }

    public function canEdit($member = null)
    {
        return $this->canManage($member);
    }

    public function canDelete($member = null)
    {
        return $this->canManage($member);
    }

    public function TicketsRemaining(): int
    {
        $helper = new EventRegistrationTicketsHelper($this);

        return $helper->numberOfTicketsRemaining();
    }


    protected function canManage(?Member $member): bool
    {
        return Permission::check('ADMIN', 'any', $member) || Permission::check('EVENT_MANAGE', 'any', $member);
    }
    // ---- ticket count related helper method.  Possibly trait these ----
}
