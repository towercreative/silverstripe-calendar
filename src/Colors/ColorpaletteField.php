<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Colors;

use SilverStripe\Forms\DropdownField;
use SilverStripe\View\Requirements;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * Class ColorpaletteField
 *
 * @package TitleDK\Calendar\Colors
 */
class ColorpaletteField extends DropdownField
{

    /**
     * ColorpaletteField constructor.
     *
     * @param string|array<string> $source
     * @param \SilverStripe\Forms\Form $form
     */
    public function __construct(
        string $name,
        ?string $title = null,
        $source = null,
        string $value = "",
        ?Form $form = null
    ) {
        if (!\is_array($source)) {
            $source = ColorpaletteHelper::get_palette();
        }

        parent::__construct($name, ($title===null) ? $name : $title, $source, $value, $form);
    }


    public function Field(): string
    {
        $this->addExtraClass('ColorpaletteInput');
        ColorpaletteHelper::requirements();
        Requirements::javascript("titledk/silverstripe-calendar:javascript/admin/ColorpaletteField.js");

        $source = $this->getSource();

        //adding the current value to the mix if isn't in the array
        $val = $this->getColorWithHash();
        $this->value = $val;
        $source[$val] = $val;

        $this->setSource($source);

        return parent::Field();
    }


    /**
     * Getter that always returns the color with a hash
     * As the standard Silverstripe color picker seems to save colors without a hash,
     * this just makes sure that colors are always returned with a hash - whether they've been
     * saved with or without one
     */
    public function getColorWithHash(): string
    {
        $color = $this->value;

        return \strpos($color, '#') === false
            ? '#' . $color
            : $color;
    }
}
