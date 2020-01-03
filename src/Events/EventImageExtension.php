<?php
namespace TitleDK\Calendar\Events;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Add an image to an event
 *
 * @package calendar
 * @property \TitleDK\Calendar\Events\Event|\TitleDK\Calendar\Events\EventImageExtension $owner
 * @property int $FeaturedImageID
 * @method \SilverStripe\Assets\Image FeaturedImage()
 */
class EventImageExtension extends DataExtension
{

    private static $has_one = array(
        'FeaturedImage' => Image::class
    );

    private static $summary_fields = ['Thumbnail'];

    public function updateCMSFields(FieldList $fields)
    {
        $uploadField = new UploadField('FeaturedImage', 'Featured Image');
        $uploadField->setAllowedFileCategories('image');
        $uploadField->setFolderName('events');
        $fields->addFieldToTab('Root.Main', $uploadField);
    }

    public function getThumbnail()
    {
        error_log('FEATURED IMAGE: ' . $this->owner->FeaturedImage()->ID);
        $image = $this->owner->FeaturedImage();
        if ($image->ID > 0) {
            return $image->Fit(80, 80);
        } else {
            return '(No Image)';
        }
    }
}
