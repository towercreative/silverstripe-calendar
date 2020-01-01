<?php

namespace TitleDK\Calendar\Tests\Registrations;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use TitleDK\Calendar\Registrations\EventRegistration;

class AttendeesExtensionTest extends SapphireTest
{
    // This test does not work with Postgresql without a fixtures file
    protected static $fixture_file = 'tests/events.yml';

    public function test_update_cms_fields()
    {
        $registration = new EventRegistration();
        $fields = $registration->getCMSFields();

        /** @var TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList()->toArray();

        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields);


        $this->assertEquals([
            'Name',
            'PayersName',
            'Email',
            'Status',
            'NumberOfTickets',
            'AmountPaid',
            'Notes',
            'EventID',
            'RegistrationEmbargoAt'
        ], $names);

        $attendeesTab = $rootTab->fieldByName('Attendees');
        $fields = $attendeesTab->FieldList()->toArray();

        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields);


        $this->assertEquals([
            'Attendees'
        ], $names);
    }
}
