<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Admin\GridField;

use SilverStripe\Forms\GridField\GridFieldDetailForm;

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
 * @package calendar
 * @subpackage admin
 */
class CalendarEventGridFieldDetailForm extends GridFieldDetailForm
{
}
