<?php declare(strict_types = 1);

namespace TitleDK\Calendar\PageTypes;

use Carbon\Carbon;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use TitleDK\Calendar\Admin\GridField\CalendarEventGridFieldDetailForm;

/**
 * Event Page
 * A page that can serve as a permanent url for recurring events like festivals, monthly shopping events etc.
 *
 * Dates are added manually.
 *
 * @package calendar
 * @subpackage pagetypes
 * @method \SilverStripe\ORM\DataList|array<\TitleDK\Calendar\Events\Event> Events()
 */
class EventPage extends \Page
{

    private static $singular_name = 'Event Page';
    private static $description = 'Provides for a permanent URL for recurring events like festivals, monthly ' .
        'shopping, events etc.';

    private static $has_many = [
        'Events' => 'TitleDK\Calendar\Events\Event';
    private ];

    public function ComingEvents()
    {
        $timestamp = Carbon::now()->timestamp;

        //Coming events
        return $this->Events()
            ->filter(
                [
                    'StartDateTime:GreaterThan' => \date('Y-m-d', $timestamp - 24*60*60)
                ],
            );
    }


    public function PastEvents()
    {
        $timestamp = Carbon::now()->timestamp;

        //Past events
        return $this->Events()
            ->filter(
                [
                    'StartDateTime:LessThan' => \date('Y-m-d', $timestamp)
                ],
            );
    }


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $gridEventConfig = GridFieldConfig_RecordEditor::create();
        $gridEventConfig->removeComponentsByType(GridFieldDetailForm::class);
        $gridEventConfig->addComponent(new CalendarEventGridFieldDetailForm());

        //Coming events
        $comingEvents = $this->ComingEvents();

        $GridFieldComing = new GridField(
            'ComingEvents',
            '',
            $comingEvents,
            $gridEventConfig,
        );
        $GridFieldComing->setModelClass('TitleDK\Calendar\Events\Event');

        $fields->addFieldToTab(
            'Root.ComingEvents',
            $GridFieldComing,
        );

        //Past events
        $pastEvents = $this->PastEvents();
        $GridFieldPast = new GridField(
            'PastEvents',
            '',
            $pastEvents,
            $gridEventConfig,
        );
        $GridFieldPast->setModelClass('TitleDK\Calendar\Events\Event');

        $fields->addFieldToTab(
            'Root.PastEvents',
            $GridFieldPast,
        );

        return $fields;
    }


    /**
     * Title shown in the calendar administration
     */
    public function getCalendarTitle(): string
    {
        return $this->Title;
    }
}
