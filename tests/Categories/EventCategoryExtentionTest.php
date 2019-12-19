<?php

namespace TitleDK\Calendar\Tests\Categories;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventCategoryExtentionTest extends SapphireTest
{
    public function testUpdateCMSFields()
    {
        $event = new Event();
        $fields = $event->getCMSFields()->toArray();
        $names = array_map(function($field) {
            return $field->Name;
        }, $fields);

        // @todo go one level deeper, this is a bit of a meaningless test
        $this->assertEquals(['Root'], $names);
    }
}
