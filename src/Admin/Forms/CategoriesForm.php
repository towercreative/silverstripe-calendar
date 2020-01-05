<?php
namespace TitleDK\Calendar\Admin\Forms;

use SilverStripe\Control\Controller;
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
 * @package    calendar
 * @subpackage admin
 */
class CategoriesForm extends Form
{

    /**
     * CategoriesForm constructor.
     * @param Controller $controller
     * @param string $name
     */
    public function __construct($controller, $name)
    {

        //Administering categories
        if(Config::inst()->get(EventCategory::class, 'enabled')) {
            $gridCategoryConfig = GridFieldConfig_RecordEditor::create();
            $GridFieldCategories = new GridField(
                'Categories',
                '',
                PublicEventCategory::get(),
                $gridCategoryConfig
            );


            $fields = new FieldList(
                $GridFieldCategories
            );
            $actions = new FieldList();
            $this->addExtraClass('CategoriesForm');
            parent::__construct($controller, $name, $fields, $actions);
        }
    }
}
