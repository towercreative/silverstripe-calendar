<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Admin;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Admin\CalendarAdmin;

class CalendarAdminTest extends SapphireTest
{
    /** @var \TitleDK\Calendar\Admin\CalendarAdmin */
    private $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = new CalendarAdmin();
        $this->admin->modelClass = 'TitleDK\Calendar\Events\Event';
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
                'TitleDK\Calendar\Events\Event' => ['title' => 'Events'],
                'TitleDK\Calendar\Categories\PublicEventCategory' => ['title' => 'Categories'],
                'TitleDK\Calendar\Calendars\Calendar' => ['title' => 'Calendars'],
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
