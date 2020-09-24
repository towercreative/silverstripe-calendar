<?php

namespace TitleDK\Calendar\Tests\Admin;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Admin\CalendarAdmin;

class CalendarAdminTest extends SapphireTest
{
    /** @var CalendarAdmin */
    private $admin;

    public function setUp()
    {
        $this->admin = new CalendarAdmin();
        $this->admin->modelClass = 'TitleDK\Calendar\Events\Event';
        $this->admin->init();

        return parent::setUp();
    }


    public function testGetModelClass()
    {
        $this->assertEquals('TitleDK-Calendar-Events-Event', $this->admin->getModelClass());
    }

    public function test_get_managed_models()
    {
        $managedModels = $this->admin->getManagedModels();
        $this->assertEquals(
            [
                'TitleDK\Calendar\Events\Event' => ['title' => 'Events'],
                'TitleDK\Calendar\Categories\PublicEventCategory' => ['title' => 'Categories'],
                'TitleDK\Calendar\Calendars\Calendar' => ['title' => 'Calendars']
            ],
            $managedModels
        );
    }

    public function test_get_edit_form_events()
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Events\Event';
        $form = $this->admin->getEditForm();
        // @todo What to test here?
    }

    public function test_get_edit_form_categories()
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Categories\PublicEventCategory';
        $form = $this->admin->getEditForm();
    }

    public function test_get_edit_form_calendar()
    {
        $this->admin->modelClass = 'TitleDK\Calendar\Calendars\Calendar';
        $form = $this->admin->getEditForm();
    }


    public function test_provide_permissions()
    {
        $permissions = $this->admin->providePermissions();
        $this->assertEquals(4, sizeof($permissions));
    }
}
