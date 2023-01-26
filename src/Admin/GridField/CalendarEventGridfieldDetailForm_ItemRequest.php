<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Admin\GridField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\LiteralField;
use SilverStripe\View\Requirements;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

// @TODO is the _TemRequest mandatory on these classes or not?
// @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
/**
 * extension to the @see GridFieldDetailForm_ItemRequest
 */
class CalendarEventGridFieldDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest
{
    /** @var array<string> */
    private static $allowed_actions = [
        'edit',
        'view',
        'ItemEditForm',
    ];

    /** @return \SilverStripe\Control\HTTPResponse|\SilverStripe\Forms\Form */
    public function ItemEditForm()
    {

        //Timepicker
        Requirements::css('calendar/thirdparty/timepicker/jquery.timepicker.css');
        //Requirements::javascript('calendar/thirdparty/timepicker/jquery.timepicker.js');
        //modification to allow timepicker and timeentry to work in tandem:
        Requirements::javascript('calendar/thirdparty/timepicker/jquery.timepicker-timeentry.js');

        //Timeentry
        Requirements::javascript('calendar/thirdparty/timeentry/jquery.timeentry.js');


        //CSS/JS Dependencies
        Requirements::css("calendar/css/admin/CalendarEventGridFieldDetailForm.css");
        Requirements::javascript("calendar/javascript/events/EventFields.js");
        Requirements::javascript("calendar/javascript/admin/CalendarEventGridFieldDetailForm.js");


        $form = parent::ItemEditForm();
        if (!$form instanceof Form) {
            return $form;
        }

        $form->addExtraClass('CalendarEventGridfieldDetailForm');

        if ($this->record->ID !== 0) {
            $actionFields = $form->Actions();
            $link = Controller::join_links($this->gridField->Link('item'), 'new');

            $actionFields->push(
                new LiteralField(
                    'addNew',
                    '<a href="' . $link . '" class="action action-detail ss-ui-action-constructive ' .
                    'ss-ui-button ui-button ui-widget ui-state-default ui-corner-all new new-link" ' .
                    'data-icon="add">Add new ' . $this->record->i18n_singular_name() . '</a>',
                ),
            );
        }

        return $form;
    }
}
