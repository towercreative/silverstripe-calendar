<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Admin\Forms;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use TitleDK\Calendar\Calendars\Calendar;

/**
 * CalendarsForm
 *
 * @package calendar
 * @subpackage admin
 */
// @todo This was CMSForm
class CalendarsForm extends Form
{

    /**
     * Contructor
     */
    public function __construct(Controller $controller, string $name)
    {

        //Administering calendars
        if (!Config::inst()->get(Calendar::class, 'enabled')) {
            return;
        }

        //Configuration for calendar grid field
        $gridCalendarConfig = GridFieldConfig_RecordEditor::create();
        $gridCalendarConfig->removeComponentsByType(GridFieldDataColumns::class);
        $gridCalendarConfig->addComponent($dataColumns = new GridFieldDataColumns(), GridFieldEditButton::class);

        $c = \singleton('TitleDK\Calendar\Calendars\Calendar');
        $summaryFields = $c->summaryFields();

        $dataColumns->setDisplayFields($summaryFields);

        //settings for the case that colors are enabled
        if (Config::inst()->get(Calendar::class, 'colors')) {
            $dataColumns->setFieldFormatting(
                [
                "Title" => '<div style=\"height:20px;width:20px;display:inline-block;vertical-align:middle;' .
                    'margin-right:6px;background:$Color\"></div> $Title',
                ],
            );
        }

        $GridFieldCalendars = new GridField(
            'Calendars',
            '',
            Calendar::get(),
            $gridCalendarConfig,
        );

        $fields = new FieldList(
            $GridFieldCalendars,
        );
        $actions = new FieldList();
        $this->addExtraClass('CalendarsForm');

        parent::__construct($controller, $name, $fields, $actions);
    }
}
