<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Categories;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Categories\EventCategory;

class EventCategoryTest extends SapphireTest
{
    public function testGetAddNewFields(): void
    {
        $ec = EventCategory::singleton();

        /** @var \SilverStripe\Forms\FieldList $fields */
        $fields = $ec->getAddNewFields();
        $this->assertNull($fields->fieldByName('Events'));
        $this->assertNotNull($fields->fieldByName('Title'));
    }


    public function testGetCMSFields(): void
    {
        $ec = EventCategory::singleton();

        /** @var \SilverStripe\Forms\FieldList $fields */
        $fields = $ec->getCMSFields();
        $this->assertNull($fields->fieldByName('Events'));
    }
}
