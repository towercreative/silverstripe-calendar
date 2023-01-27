<?php declare(strict_types=1);

namespace TitleDK\Calendar\Tests\Admin;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Admin\CalendarAdmin;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Categories\PublicEventCategory;
use TitleDK\Calendar\Events\Event;

class CalendarAdminTest extends SapphireTest
{
    /** @var \TitleDK\Calendar\Admin\CalendarAdmin */
    private $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = CalendarAdmin::create();
        $this->admin->modelClass = Event::class;
        $this->admin->init();
    }


    public function testGetModelClass(): void
    {
        $this->assertEquals('TitleDK-Calendar-Events-Event', $this->admin->getModelClass());
    }


    public function testGetManagedModels(): void
    {
        $managedModels = $this->admin->getManagedModels();
        $this->assertEquals(
            [
                Event::class => [
                    'title' => 'Events',
                    'dataClass' => Event::class,
                ],
                PublicEventCategory::class => [
                    'title' => 'Categories',
                    'dataClass' => PublicEventCategory::class
                ],
                Calendar::class => [
                    'title' => 'Calendars',
                    'dataClass' => Calendar::class,
                ],
            ],
            $managedModels,
        );
    }


    public function testGetEditFormEvents(): void
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Events\Event';
        //$form = $this->admin->getEditForm();
        $this->markTestIncomplete('No assertions');
    }


    public function testGetEditFormCategories(): void
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Categories\PublicEventCategory';
        //$form = $this->admin->getEditForm();
        $this->markTestIncomplete('No assertions');
    }


    public function testGetEditFormCalendar(): void
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Calendars\Calendar';
        // @todo What to test here?
        // $form = $this->admin->getEditForm();
        $this->markTestIncomplete('No assertions');
    }


    public function testProvidePermissions(): void
    {
        $permissions = $this->admin->providePermissions();
        $this->assertEquals(4, \sizeof($permissions));
    }
}
