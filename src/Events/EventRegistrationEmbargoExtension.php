<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Events;

use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;

// @phpcs:disable Generic.Files.LineLength.TooLong
// @phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong

/**
 * Add an image to an event
 *
 * @package calendar
 * @property \TitleDK\Calendar\Events\Event|\TitleDK\Calendar\Registrations\EventRegistration|\TitleDK\Calendar\Events\EventRegistrationEmbargoExtension $owner
 * @property string $RegistrationEmbargoAt
 */
class EventRegistrationEmbargoExtension extends DataExtension
{

    private static $db = [
        'RegistrationEmbargoAt' => DBDatetime::class,
    ];

    private static $summary_fields = [
        'RegistrationEmbargoAt' => 'Embargo Registration At',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $relativeTimeEmbargo = $this->owner->config()->get('embargo_registration_relative_to_end_datetime_mins');

        $embargoField = new DatetimeField('RegistrationEmbargoAt');

        $rightTitle = 'If this field is left blank, registration will be embargoed ';
        $rightTitle .= $relativeTimeEmbargo < 0
            ? 'before'
            : 'after';
        $rightTitle .= $relativeTimeEmbargo . ' minutes relative to the end datetime of the event';
        $embargoField->setRightTitle($rightTitle);
        $fields->addFieldToTab(
            'Root.Main',
            $embargoField,
            'CalendarID'
        );
    }
}
