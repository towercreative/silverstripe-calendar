<?php
namespace TitleDK\Calendar\Admin;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\Requirements;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Categories\EventCategory;

/**
 * Calendar Admin
 *
 * @package calendar
 * @subpackage admin
 */
class CalendarAdmin extends ModelAdmin implements PermissionProvider
{

    private static $menu_title = "Calendar";
    private static $url_segment = "calendar";

    private static $allowed_actions = array(
        'CalendarsForm',
        'CategoriesForm',
        'EventsForm'
    );

    private static $managed_models = array(
        'TitleDK\Calendar\Events\Event',
        'TitleDK\Calendar\Categories\PublicEventCategory',
        'TitleDK\Calendar\Calendars\Calendar'
    );

    private static $model_importers = array(
        'TitleDK\Calendar\Events\Event' => 'TitleDK\Calendar\Events\EventCsvBulkLoader',
        'TitleDK\Calendar\Categories\PublicEventCategory' => 'SilverStripe\Dev\CsvBulkLoader',
        'TitleDK\Calendar\Calendars\Calendar' => 'SilverStripe\Dev\CsvBulkLoader'
    );


    private static $menu_icon = "titledk/silverstripe-calendar:images/icons/calendar.png";

    public function init()
    {
        parent::init();


        //CSS/JS Dependencies - currently not much there
        Requirements::css("titledk/silverstripe-calendar:css/admin/CalendarAdmin.css");
        Requirements::javascript("titledk/silverstripe-calendar:javascript/admin/CalendarAdmin.js");
    }

    public function getModelClass()
    {
        return $this->sanitiseClassName($this->modelClass);
    }

    public function getManagedModels()
    {
        // Unset managed models according to config
        /**
 * @todo change to use config API
*/
        $models = parent::getManagedModels();
        if (!$this->calendarsEnabled()
            && isset($models['Calendar'])
        ) {
            unset($models['Calendar']);
        }
        if (!$this->categoriesEnabled()
            && isset($models['Calendar'])
        ) {
            unset($models['PublicEventCategory']);
        }
        return $models;
    }

    protected function determineFormClass()
    {
        switch ($this->modelClass) {
            case 'TitleDK\Calendar\Calendars\Calendar':
                $class = 'TitleDK\Calendar\Admin\Forms\CalendarsForm';
                break;
            case 'TitleDK\Calendar\Categories\PublicEventCategory':
                $class = 'TitleDK\Calendar\Admin\Forms\CategoriesForm';
                break;
            case 'TitleDK\Calendar\Events\Event':
                $class = 'TitleDK\Calendar\Admin\Forms\EventsForm';
                break;
            default:
                $class = 'SilverStripe\Forms\Form'; // @todo was CMSForm
                break;
        }

        return $class;
    }

    public function getEditForm($id = null, $fields = null)
    {
        $list = $this->getList();
        $exportButton = new GridFieldExportButton('buttons-before-left');
        $exportButton->setExportColumns($this->getExportFields());
        $listField = GridField::create(
            $this->sanitiseClassName($this->modelClass),
            false,
            $list,
            $fieldConfig = GridFieldConfig_RecordEditor::create($this->stat('page_length'))
                ->addComponent($exportButton)
                ->removeComponentsByType(GridFieldFilterHeader::class)
                ->addComponents(new GridFieldPrintButton('buttons-before-left'))
        );

        // Validation
        if (singleton($this->modelClass)->hasMethod('getCMSValidator')) {
            $detailValidator = singleton($this->modelClass)->getCMSValidator();
            $listField->getConfig()->getComponentByType(GridFieldDetailForm::class)->setValidator($detailValidator);
        }

        if ($this->showImportForm) {
            $fieldConfig->addComponent(
                GridFieldImportButton::create('buttons-before-left')
                    ->setImportForm($this->ImportForm())
                    ->setModalTitle(_t('SilverStripe\\Admin\\ModelAdmin.IMPORT', 'Import from CSV'))
            );
        }

        $formClass = $this->determineFormClass();

        $form = $formClass::create(
            $this,
            'EditForm',
            new FieldList($listField),
            new FieldList()
        )->setHTMLID('Form_EditForm');

        // @todo This method does not exist $form->setResponseNegotiator($this->getResponseNegotiator());
        $form->addExtraClass('cms-edit-form cms-panel-padded center');
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
        $editFormAction = Controller::join_links($this->Link($this->sanitiseClassName($this->modelClass)), 'EditForm');
        $form->setFormAction($editFormAction);
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');

        $this->extend('updateEditForm', $form);

        return $form;
    }

    protected function calendarsEnabled()
    {
        return Config::inst()->get(Calendar::class, 'enabled');
    }

    protected function categoriesEnabled()
    {
        return Config::inst()->get(EventCategory::class, 'enabled');
    }

    public function providePermissions()
    {
        $title = LeftAndMain::menu_title($this->class);
        return array(
            "CMS_ACCESS_CalendarAdmin" => array(
                'name' => _t('CMSMain.ACCESS', "Access to '{title}' section", array('title' => $title)),
                'category' => _t('Permission.CMS_ACCESS_CATEGORY', 'CMS Access'),
                'help' => 'Allow access to calendar management module.'
            ),
            "CALENDAR_MANAGE" => array(
                'name' => _t('CalendarAdmin.CALENDAR_MANAGE', 'Manage calendars'),
                'category' => _t('CalendarAdmin.CALENDAR_PERMISSION_CATEGORY', 'Calender'),
                'help' => 'Allow creating, editing, and deleting calendars.'
            ),
            "EVENTCATEGORY_MANAGE" => array(
                'name' => _t('CalendarAdmin.EVENTCATEGORY_MANAGE', 'Manage event categories'),
                'category' => _t('CalendarAdmin.CALENDAR_PERMISSION_CATEGORY', 'Calender'),
                'help' => 'Allow creating, editing, and deleting event categories.'
            ),
            "EVENT_MANAGE" => array(
                'name' => _t('CalendarAdmin.EVENT_MANAGE', 'Manage events'),
                'category' => _t('CalendarAdmin.CALENDAR_PERMISSION_CATEGORY', 'Calender'),
                'help' => 'Allow creating, editing, and deleting events.'
            )
        );
    }
}
