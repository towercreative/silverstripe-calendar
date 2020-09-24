<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Admin\GridField;

use SilverStripe\Dev\SapphireTest;

class CalendarEventPageGridfieldDetailFormItemRequestTest extends SapphireTest
{
    public function testItemEditForm(): void
    {

        $this->markTestSkipped('TODO');
        /*
        $ir = new CalendarEventGridFieldDetailForm_ItemRequest(null, null, null, null, null);

        $form = $ir->ItemEditForm();
        $fields = $form->Fields();
        $names = array_map(function($field) {
            return $field->Name;
        }, $fields->toArray());

        $this->assertEquals([], $names);
        */
    }
}
