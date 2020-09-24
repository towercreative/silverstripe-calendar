<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Admin;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Admin\CalendarAdmin;

class CalendarAdminTest extends SapphireTest
{
    /** @var \TitleDK\Calendar\Admin\CalendarAdmin */
    private $admin;

    public function setUp()
    {
        $this->admin = new CalendarAdmin();
        $this->admin->modelClass = 'TitleDK\Calendar\Events\Event';
        $this->admin->init();

        return parent::setUp();
    }


    public function testGetModelClass(): void
    {
        $this->assertEquals('TitleDK-Calendar-Events-Event', $this->admin->getModelClass());
    }


    public function test_get_managed_models(): void
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


    public function test_get_edit_form_events(): void
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Events\Event';
        $form = $this->admin->getEditForm();
        // @todo What to test here?
    }


    public function test_get_edit_form_categories(): void
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Categories\PublicEventCategory';
        $form = $this->admin->getEditForm();
    }


    public function test_get_edit_form_calendar(): void
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Calendars\Calendar';
        $form = $this->admin->getEditForm();
    }


    public function test_provide_permissions(): void
    {
        $permissions = $this->admin->providePermissions();
        $this->assertEquals(4, \sizeof($permissions));
    }
}
