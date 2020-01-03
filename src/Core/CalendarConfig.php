<?php
namespace TitleDK\Calendar\Core;

use TitleDK\Calendar\Events\Event;

/**
 * Calendar Config
 *
 * NOTE: This module will not function properly without having been initialized through the
 * project specific _config.php file. At the minimum, you need to add the following line:
 *
 * CalendarConfig::init();
 *
 * As calendar implementations often differ substantially, the calendar module
 * can be configured through this file.
 * The configuration shown here is the calendar with all basic features enabled.
 * This will seldom be the case, and hence it's expected that the configuration is amended
 * when the module is instantiated in the project _config.php file.
 *
 *
 * ...and YES, I know SilverStripe 3.1 has a built-in config system, this
 * has been coded prior to this though - pull requests welcome ;)
 *
 * @package    calendar
 * @subpackage core
 */
class CalendarConfig
{

    /**
     * Base calendar settings
     * All basic features are enabled
     *
     * @var type
     */
    protected static $settings = array(
        'enabled' => true,
        //the Silverstripe version can be set to skip the need for several branches
        //can be: default, 3.0, 3.1 (other will be added later)
        'ssversion' => 'default',
        //the events subpackage is needed and cannot be disabled,
        //but it can be configured

        //the admin subpackage is enabled by default and is currently
        //not configuratble
        'admin' => array(),
        //the pagetypes subpackage is enabled by default
        'pagetypes' => array(
            'calendarpage' => array(
                //'eventlist' => true,
               // 'calendarview' => true, //fullcalendar
                //'index' => 'eventlist',
                'controllerUrl' => '/fullcalendar/',
                'fullcalendar_js_settings' => "
					header: {
						left: '',
						//center: 'title'
						//right: 'title'
						//left: 'prev, next',
						center: 'title',
						//right: 'month,basicWeek'
					},
					// add more space for events
					height: 'auto'
				"
            )
        ),
        'calendars' => array(
            //'enabled' => true,
            //'colors' => true,
            //allowing calendars to be shaded
            //this can be used with calendars containing secondary information
            //'shading' => false
        ),
        'categories' => array(
            //'enabled' => true,
            //colors not yet implemented:
            //'colors' => true
        ),
        'colors' => array(
            'enabled' => true,

            'basepalette' => array(
                '#4B0082' => '#4B0082',
                '#696969' => '#696969',
                '#B22222' => '#B22222',
                '#A52A2A' => '#A52A2A',
                '#DAA520' => '#DAA520',
                '#006400' => '#006400',
                '#40E0D0' => '#40E0D0',
                '#0000CD' => '#0000CD',
                '#800080' => '#800080',
            )
        )
    );

    /**
     * Config setter & getter
     * This serves as a settings setter and getter at the same time
     */
    public static function settings($settings = null)
    {
        if ($settings) {
            //set mode
            self::$settings = self::mergeSettings(self::$settings, $settings);
        } else {
            //get mode
        }

        //always return settings
        return self::$settings;
    }

    /**
     * method for merging setting files
     */
    protected static function mergeSettings($Arr1, $Arr2)
    {
        foreach ($Arr2 as $key => $Value) {
            if (array_key_exists($key, $Arr1) && is_array($Value)) {
                $Arr1[$key] = self::mergeSettings($Arr1[$key], $Arr2[$key]);
            } else {
                $Arr1[$key] = $Value;
            }
        }
        return $Arr1;
    }

    /**
     * Getter for subpackage specific settings
     *
     * @param  string $subpackage
     * @return array
     */
    public static function subpackage_settings($subpackage)
    {
        $s = self::settings();
        if (isset($s[$subpackage])) {
            return $s[$subpackage];
        }
    }

    /**
     * Getter for a specific setting from a subpackage
     *
     * @param string $subpackage
     * @param string $setting
     */
    public static function subpackage_setting($subpackage, $setting)
    {
        $s = self::subpackage_settings($subpackage);
        if (isset($s[$setting])) {
            return $s[$setting];
        }
    }

}
