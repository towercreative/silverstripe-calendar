<?php
namespace TitleDK\Calendar\Admin\Forms;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\GridField\GridField;
use TitleDK\Calendar\Admin\GridField\CalendarEventGridFieldDetailForm;
use TitleDK\Calendar\Core\CalendarConfig;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\EventPage_Controller;

/**
 * Events Form
 *
 * @package calendar
 * @subpackage admin
 */
class EventsForm extends Form
{

    public static function eventConfig()
    {
        $gridEventConfig = GridFieldConfig_RecordEditor::create();

        //Custom detail form
        $gridEventConfig->removeComponentsByType(GridFieldDetailForm::class);
        $gridEventConfig->addComponent(new CalendarEventGridFieldDetailForm());

        //Custom columns
        $gridEventConfig->removeComponentsByType(GridFieldDataColumns::class);
        $dataColumns = new GridFieldDataColumns();

        $summaryFields = Config::inst()->get(Event::class, 'summary_fields');
        //Show the page if the event is connected to an event page
        if (CalendarConfig::subpackage_setting('pagetypes', 'enable_eventpage')) {
            $summaryFields['getEventPageCalendarTitle'] = 'Page';
        }

        //event classname - we might not always want it here - but here it is - for now
        $summaryFields['i18n_singular_name'] = 'Type';

        $dataColumns->setDisplayFields($summaryFields);

        $gridEventConfig->addComponent($dataColumns, GridFieldEditButton::class);

        return $gridEventConfig;
    }

    /**
     * Contructor
     * @param EventPage_Controller $controller
     * @param string $name
     */
    public function __construct($controller, $name)
    {
        $fields = FieldList::create();
        $fields->push(TabSet::create("Root"));
        $gridConfig = self::eventConfig();

        $comingGridField = GridField::create(
            'ComingEvents',
            '',
            CalendarHelper::coming_events(),
            $gridConfig
        );
        $fields->addFieldToTab('Root.Coming', $comingGridField);

        // Find all past events, including those with null start time
        $time = date('Y-m-d', time());
        $pastEvents = Event::get()
            ->where("\"StartDateTime\" < '$time' OR \"StartDateTime\" IS NULL")
            ->sort('StartDateTime DESC');

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
}
