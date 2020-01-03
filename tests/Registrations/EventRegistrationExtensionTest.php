<?php

namespace TitleDK\Calendar\Tests\Registrations;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\CalendarPage;

class EventRegistrationExtensionTest extends SapphireTest
{
    protected static $fixture_file = ['tests/registered-events.yml'];

    /** @var Event */
    private $event;

    public function setUp()
    {
         parent::setUp();
         $this->event = $this->objFromFixture(Event::class, 'conference');
    }

    public function testUpdateCMSFields()
    {
        $fields = $this->event->getCMSFields();
        /** @var TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();

        // order of categories and featured image field incosistent over CI, as such tweak the test
        // assert field exists
        // remove it
        $categoriesField = $fields->fieldByName('Categories');
        $this->assertNotNull($categoriesField);
        $fields->removeByName('Categories');

        $names = array_map(function ($field) {
            return $field->Name;
        }, $fields->toArray());

        $this->assertEquals([
            'Title',
            'StartDateTime',
            'AllDay',
            'TimeFrameHeader',
            'TimeFrameType',
            'Clear', // @todo what is this
            'RegistrationEmbargoAt',
            'CalendarID',
            'Tags',
            'FeaturedImage'
        ], $names);

        // now the registrations tab
        /** @var Tab $registrationsTab */
        $registrationsTab = $rootTab->fieldByName('Registrations');
        $fields = $registrationsTab->FieldList();

        $names = array_map(function ($field) {
            return $field->Name;
        },
            $fields->toArray());

        $this->assertEquals([
            'Header1',
            'Registerable',
            'Header2',
            'RSVPEmail',
            'Header3',
            'TicketsRequired',
            'NumberOfAvailableTickets',
            'PaymentRequired',
            'Header4',
            'Cost',
            'Registrations'
        ], $names);
    }

    public function test_get_register_link()
    {
        $conferencePage = $this->objFromFixture(CalendarPage::class, 'calendarpageconference');
        $expected = '/conference-page/register/' .
            $this->event->ID;
        $this->assertEquals($expected, $this->event->getRegisterLink());
    }

    public function testRegistrationForm()
    {
        $this->markTestSkipped('TODO');
    }

    public function testRegistrationPaymentForm()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetExportableRegistrationsList()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetExportFields()
    {
        $this->markTestSkipped('TODO');
    }
}
