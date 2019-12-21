<?php

namespace TitleDK\Calendar\Tests\Categories;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Categories\PublicEventCategory;

class PublicEventCategoryTest extends SapphireTest
{
    protected static $fixture_file = 'tests/events.yml';

    /** @var PublicEventCategory */
    private $category1;

    /** @var PublicEventCategory */
    private $category2;

    public function setUp()
    {
        // this has to happen before accessing the fixtures
        parent::setUp();

        $this->category1 = $this->objFromFixture('TitleDK\Calendar\Categories\PublicEventCategory', 'category1');
        $this->category2 = $this->objFromFixture('TitleDK\Calendar\Categories\PublicEventCategory', 'category2');
    }

    public function testComingEvents()
    {
        $category1 = $this->objFromFixture('TitleDK\Calendar\Categories\PublicEventCategory', 'category1');

        /** @var PublicEventCategory $category1 */
        $events = $this->category1->comingEvents()->toArray();
        $titles = array_map(function($event) {
            return $event->Title;
        }, $events);

        $this->assertEquals([], $titles);

        foreach($events as $event) {
            error_log($event->Title);
        }

    }

    public function testCanView()
    {
        $this->assertTrue($this->category1->canView());
    }

    public function testCanCreate()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanEdit()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanDelete()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanManage()
    {
        $this->markTestSkipped('TODO');
    }
}
