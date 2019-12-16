<?php

namespace TitleDK\Calendar\Tests\Categories;

use \SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use TitleDK\Calendar\Categories\EventCategory;

class EventCategoryTest extends SapphireTest
{
    public function testGetAddNewFields()
    {
        $ec = new EventCategory();

        /** @var FieldList $fields */
        $fields = $ec->getAddNewFields();
        $this->assertNull($fields->fieldByName('Events'));
        $this->assertNotNull($fields->fieldByName('Title'));
    }

    public function testGetCMSFields()
    {
        $ec = new EventCategory();

        /** @var FieldList $fields */
        $fields = $ec->getCMSFields();
        $this->assertNull($fields->fieldByName('Events'));
    }
}
