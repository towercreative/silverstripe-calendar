<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Admin\Forms;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\TabSet;
use TitleDK\Calendar\Admin\GridField\CalendarEventGridFieldDetailForm;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\EventPage;

/**
 * Events Form
 *
 * @package calendar
 * @subpackage admin
 */
class EventsForm extends Form
{

    /**
     * Contructor
     *
     * @param \TitleDK\Calendar\PageTypes\EventPageController $controller
     */
    public function __construct(EventPage_Controller $controller, string $name)
    {
        $fields = FieldList::create();
        $fields->push(TabSet::create("Root"));
        $gridConfig = self::eventConfig();

        $comingGridField = GridField::create(
            'ComingEvents',
            '',
            CalendarHelper::comingEvents(),
            $gridConfig
        );
        $fields->addFieldToTab('Root.Coming', $comingGridField);

        // Find all past events, including those with null start time
        $time = \date('Y-m-d', \time());
        $pastEvents = Event::get()
            ->where("\"StartDateTime\" < '$time' OR \"StartDateTime\" IS NULL")
            ->sort('"StartDateTime" DESC');

        $pastGridField = GridField::create(
            'PastEvents',
            '',
            $pastEvents,
            $gridConfig
        );

        $fields->addFieldToTab('Root.Past', $pastGridField);

        /*
         * Actions / init
         */
        $actions = FieldList::create();

        parent::__construct($controller, $name, $fields, $actions);
    }


    public static function eventConfig(): GridFieldConfig_RecordEditor
    {
        $gridEventConfig = GridFieldConfig_RecordEditor::create();

        //Custom detail form
        $gridEventConfig->removeComponentsByType(GridFieldDetailForm::class);
        $gridEventConfig->addComponent(new CalendarEventGridFieldDetailForm());

        //Custom columns
        $gridEventConfig->removeComponentsByType(GridFieldDataColumns::class);
        $dataColumns = new GridFieldDataColumns();

        $summaryFields = Config::inst()->get(Event::class, 'summary_fields');
        $eventPageEnabled = Config::inst()->get(EventPage::class, 'enable_eventpage');
        //Show the page if the event is connected to an event page
        if ($eventPageEnabled) {
            $summaryFields['getEventPageCalendarTitle'] = 'Page';
        }

        //event classname - we might not always want it here - but here it is - for now
        $summaryFields['i18n_singular_name'] = 'Type';

        $dataColumns->setDisplayFields($summaryFields);

        $gridEventConfig->addComponent($dataColumns, GridFieldEditButton::class);

        return $gridEventConfig;
    }
}
