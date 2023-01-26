<?php declare(strict_types = 1);

namespace TitleDK\Calendar\DateTime;

use Carbon\Carbon;

trait DateTimeHelper
{
    /**
     * @param string $ssDateTimeString time returned from a SilverStripe DateField or DateTimeField
     * @todo Timezones
     * @return \Carbon\Carbon same time, but in Carbon
     */
    public function carbonDateTime(?string $ssDateTimeString): Carbon
    {
        //2018-05-21 13:04:00
        return Carbon::createFromFormat('Y-m-d H:i:s', $ssDateTimeString);
    }


    /** @return string the date formatted in year month day hour min sec */
    public function getSSDateTimeFromCarbon(Carbon $carbonDate): string
    {
        return $carbonDate->format('Y-m-d H:i:s');
    }
}
