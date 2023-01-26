<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Registrations;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventRegistrationExtensionTest extends SapphireTest
{
    protected static $fixture_file = ['tests/registered-events.yml'];

    /** @var \TitleDK\Calendar\Events\Event */
    private $event;

    public function setUp(): void
    {
         parent::setUp();

         $this->event = $this->objFromFixture(Event::class, 'conference');
    }


    public function testUpdateCMSFields(): void
    {
        $fields = $this->event->getCMSFields();
        /** @var \SilverStripe\Forms\TabSet $rootTab */
        $rootTab = $fields->fieldByName('Root');

        /** @var \SilverStripe\Forms\Tab $mainTab */
        $mainTab = $rootTab->fieldByName('Main');
        $fields = $mainTab->FieldList();

        // order of categories and featured image field incosistent over CI, as such tweak the test
        // assert field exists
        // remove it
        $categoriesField = $fields->fieldByName('Categories');
        $this->assertNotNull($categoriesField);
        $fields->removeByName('Categories');

        $names = \array_map(static function ($field) {
            return $field->Name;
        }, $fields->toArray());

        $this->assertEquals([
            'Title',
            'StartDateTime',
            'AllDay',
            'TimeFrameHeader',
            'TimeFrameType',
            // @todo what is this
            'Clear',
            'RegistrationEmbargoAt',
            'CalendarID',
            'Tags',
            'FeaturedImage',
        ], $names);

        // now the registrations tab
        /** @var \SilverStripe\Forms\Tab $registrationsTab */
        $registrationsTab = $rootTab->fieldByName('Registrations');
        $fields = $registrationsTab->FieldList();

        $names = \array_map(
            static function ($field) {
                return $field->Name;
            },
            $fields->toArray(),
        );

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
            'Registrations',
        ], $names);
    }


    public function testGetRegisterLink(): void
    {
        $expected = '/conference-page/register/' .
            $this->event->ID;
        $this->assertEquals($expected, $this->event->getRegisterLink());
    }


    public function testRegistrationForm(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testRegistrationPaymentForm(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testGetExportableRegistrationsList(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testGetExportFields(): void
    {
        $this->markTestSkipped('TODO');
    }
}
