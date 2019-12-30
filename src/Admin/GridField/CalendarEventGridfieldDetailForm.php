<?php
namespace TitleDK\Calendar\Admin\GridField;

use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\Form;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;

/**
 * CalendarEvent Gridfield DetailForm
 * Add additional features to the gridfield detail form:
 * 1. The classname 'CalendarEventGridfieldDetailForm' to be able to hook up css and js to the form elements
 * 2. Adding js/css requirements
 * 3. "Add New" button
 *
 * Draws on, and inspired by
 * https://github.com/webbuilders-group/GridFieldDetailFormAddNew/blob/master/gridfield/GridFieldDetailFormAddNew.php
 *
 * @package    calendar
 * @subpackage admin
 */
class CalendarEventGridFieldDetailForm extends GridFieldDetailForm
{
}
