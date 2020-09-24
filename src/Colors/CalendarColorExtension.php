<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Colors;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Color Extension
 * Allows calendars or categories to have colors
 *
 * @package calendar
 * @subpackage colors
 * @property \TitleDK\Calendar\Calendars\Calendar|\TitleDK\Calendar\Colors\CalendarColorExtension $owner
 * @property string $Color
 */
class CalendarColorExtension extends DataExtension
{
    private static $db = array(
        'Color' => 'Varchar'
    );

    public function TextColor()
    {
        $colorWithHash = $this->owner->getColorWithHash();

        return $this->owner->calculateTextColor($colorWithHash);
    }


    /**
     * Text Color calculation
     * From http://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
     * Here is a discussion on that topic:
     * http://stackoverflow.com/questions/1331591/given-a-background-color-black-or-white-text
     */
    public function calculateTextColor(string $color): string
    {
        $c = \str_replace('#', '', $color);
        $rgb[0] = \hexdec(\substr($c, 0, 2));
        $rgb[1] = \hexdec(\substr($c, 2, 2));
        $rgb[2] = \hexdec(\substr($c, 4, 2));

        return $rgb[0]+$rgb[1]+$rgb[2]<382
            ? '#fff'
            : '#000';
    }


    /**
     * Getter that always returns the color with a hash
     * As the standard Silverstripe color picker seems to save colors without a hash,
     * this just makes sure that colors are always returned with a hash - whether they've been
     * saved with or without one
     */
    public function getColorWithHash()
    {
        $color = $this->owner->Color;

        return \strpos($color, '#') === false
            ? '#' . $color
            : $color;
    }


    public function updateCMSFields(FieldList $fields): void
    {
        $colors = ColorpaletteHelper::get_palette();

        $fields->removeByName('Color');
        $fields->addFieldToTab(
            'Root.Main',
            new ColorpaletteField('Color', 'Colour', $colors),
        );
    }
}
