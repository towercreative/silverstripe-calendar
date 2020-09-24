<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Colors;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use SilverStripe\View\Requirements;

/**
 * Color palette helper
 * Helper for working with and configuring color palettes
 *
 * Resources:
 *
 * List of colors:
 * http://www.imagemagick.org/script/color.php
 */
class ColorpaletteHelper
{

    public static function requirements(): void
    {
        Requirements::javascript('titledk/silverstripe-calendar:thirdparty/colorpicker/jquery.colourPicker.mod.js');
        Requirements::css('titledk/silverstripe-calendar:thirdparty/colorpicker/jquery.colourPicker.css');
    }


    public static function palette_dropdown($name)
    {
        return DropdownField::create($name)
            ->setSource(self::get_palette())
            ->setEmptyString('-- select color --');
    }


    /**
     * Getting a color palette
     * For now we only have a hsv palette, could be extended with more options
     *
     * Potential options:
     * Standard CKEditor color palette
     * http://stackoverflow.com/questions/13455922/display-only-few-desired-colors-in-a-ckeditor-palette
     * 000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF
     *
     * Consider adding color names like this:
     * http://stackoverflow.com/questions/2993970/function-that-converts-hex-color-values-to-an-approximate-color-name
     *
     * Color variation:
     * http://stackoverflow.com/questions/1177826/simple-color-variation
     *
     * @return array Colors mapped as #color -> #color
     */
    public static function get_palette(): array
    {
        //overwriting with the palette from the calendar settings
        $colors = Config::inst()->get(ColorpaletteHelper::class, 'base_palette');

        $result = [];
        foreach ($colors as $color) {
            $result[$color] = $color;
        }

        return $result;
    }
}
