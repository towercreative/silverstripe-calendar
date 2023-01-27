<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Colors;

class ColorHelper
{

    /**
     * Text Color calculation
     * From http://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
     * Here is a discussion on that topic:
     * http://stackoverflow.com/questions/1331591/given-a-background-color-black-or-white-text
     */
    public static function calculateTextcolor(string $color): string
    {
        $rgb=[];
        $c = \str_replace('#', '', $color);
        $rgb[0] = \hexdec(\substr($c, 0, 2));
        $rgb[1] = \hexdec(\substr($c, 2, 2));
        $rgb[2] = \hexdec(\substr($c, 4, 2));

        return $rgb[0]+$rgb[1]+$rgb[2]<382
            ? '#fff'
            : '#000';
    }
}
