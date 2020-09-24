<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Categories;

use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Allowing events to have many-many categories
 *
 * @package calendar
 * @subpackage categories
 * @property \TitleDK\Calendar\Events\Event|\TitleDK\Calendar\Categories\EventCategoryExtension $owner
 * @method \SilverStripe\ORM\ManyManyList|array<\TitleDK\Calendar\Categories\EventCategory> Categories()
 */
class EventCategoryExtension extends DataExtension
{

    private static $belongs_many_many = [
        'Categories' => EventCategory::class,
    ];


    public function updateCMSFields(FieldList $fields): void
    {
        $categories = static fn () => PublicEventCategory::get()->map()->toArray();
        $categoriesField = CheckboxSetField::create('Categories', 'Categories')
            ->setSource($categories());

        //If the quickaddnew module is installed, use it to allow
        //for easy adding of categories
        if (\class_exists('QuickAddNewExtension')) {
            $categoriesField->useAddNew('PublicEventCategory', $categories);
        }

        $fields->addFieldToTab('Root.Main', $categoriesField);
    }
}
