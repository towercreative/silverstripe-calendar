<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Registrations;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Registrations\EventRegistration;

class EventRegistrationTest extends SapphireTest
{
    protected static $fixture_file = 'tests/registered-events.yml';

    /** @var \TitleDK\Calendar\Events\Event */
    private $conference;

    public function setUp(): void
    {
        parent::setUp();

        $this->conference = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'conference');
    }


    public function testGetFrontEndFields(): void
    {
        $fields = $this->conference->getFrontEndFields()->toArray();
        $names = \array_map(static function ($field) {
            return $field->Name;
        }, $fields);
        $this->assertEquals(['Title', 'AllDay', 'StartDateTime', 'TimeFrameHeader', 'TimeFrameType', 'Clear',
            'CalendarID'], $names);
    }


    public function testGetRegistrationCode(): void
    {
       // $this->generateFixtures();
        $registration = $this->objFromFixture(EventRegistration::class, 'registration10');
        $id = $registration->ID;
        $this->assertEquals('EXAMPLE-CONFERENCE-1-00' . $id, $registration->getRegistrationCode());
    }
}
