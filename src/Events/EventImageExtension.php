<?php declare(strict_types = 1);

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

    private static $has_one = [
        'FeaturedImage' => Image::class,
    ];

    private static $summary_fields = ['Thumbnail'];

    public function updateCMSFields(FieldList $fields): void
    {
        $uploadField = new UploadField('FeaturedImage', 'Featured Image');
        $uploadField->setAllowedFileCategories('image');
        $uploadField->setFolderName('events');
        $fields->addFieldToTab('Root.Main', $uploadField);
    }


    /** @return \SilverStripe\Assets\Storage\AssetContainer|string */
    public function getThumbnail()
    {
        $image = $this->owner->FeaturedImage();

        return $image->ID > 0
            ? $image->Fit(80, 80)
            : '(No Image)';
    }
}
