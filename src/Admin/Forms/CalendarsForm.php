<?php
namespace TitleDK\Calendar\Admin\Forms;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Categories\EventCategory;
use TitleDK\Calendar\Core\CalendarConfig;

/**
 * CalendarsForm
 *
 * @package    calendar
 * @subpackage admin
 */
class CalendarsForm extends Form // @todo This was CMSForm
{

    /**
     * Contructor
     *
     * @param type $controller
     * @param type $name
     */
    public function __construct($controller, $name)
    {

        //Administering calendars
        if (Config::inst()->get(Calendar::class, 'enabled')) {
            //Configuration for calendar grid field
            $gridCalendarConfig = GridFieldConfig_RecordEditor::create();
            $gridCalendarConfig->removeComponentsByType(GridFieldDataColumns::class);
            $gridCalendarConfig->addComponent($dataColumns = new GridFieldDataColumns(), GridFieldEditButton::class);

            $c = singleton('TitleDK\Calendar\Calendars\Calendar');
            $summaryFields = $c->summaryFields();

            $shading = Config::inst()->get(Calendar::class, 'shading');

            //show shading info in the gridfield
            if ($shading) {
                $summaryFields['Shaded'] = 'Shaded';
            }

            $dataColumns->setDisplayFields($summaryFields);

            //settings for the case that colors are enabled
            if (Config::inst()->get(Calendar::class, 'colors')) {
                $dataColumns->setFieldFormatting(
                    [
                    "Title" => '<div style=\"height:20px;width:20px;display:inline-block;vertical-align:middle;' .
                        'margin-right:6px;background:$Color\"></div> $Title'
                    ]
                );
            }



            $GridFieldCalendars = new GridField(
                'Calendars',
                '',
                Calendar::get(),
                $gridCalendarConfig
            );



            $fields = new FieldList(
                $GridFieldCalendars
            );
            $actions = new FieldList();
            $this->addExtraClass('CalendarsForm');
            parent::__construct($controller, $name, $fields, $actions);
        }
    }
}
