<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Admin\Forms;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use TitleDK\Calendar\Categories\EventCategory;
use TitleDK\Calendar\Categories\PublicEventCategory;

/**
 * Categories Form
 *
 * @package calendar
 * @subpackage admin
 */
class CategoriesForm extends Form
{

    /**
     * CategoriesForm constructor.
     */
    public function __construct(Controller $controller, string $name)
    {

        //Administering categories
        if (!Config::inst()->get(EventCategory::class, 'enabled')) {
            return;
        }

        $gridCategoryConfig = GridFieldConfig_RecordEditor::create();
        $GridFieldCategories = new GridField(
            'Categories',
            '',
            PublicEventCategory::get(),
            $gridCategoryConfig,
        );


        $fields = new FieldList(
            $GridFieldCategories,
        );
        $actions = new FieldList();
        $this->addExtraClass('CategoriesForm');

        parent::__construct($controller, $name, $fields, $actions);
    }
}
