<?php declare(strict_types = 1);

namespace TitleDK\Calendar\DateTime;

use Carbon\Carbon;

trait DateTimeHelperTrait
{
    /**
     * @param string $ssDateTimeString time returned from a SilverStripe DateField or DateTimeField
     * @todo Timezones
     * @return \Carbon\Carbon same time, but in Carbon
     */
    public function carbonDateTime(string $ssDateTimeString): Carbon
    {
        //2018-05-21 13:04:00
        return Carbon::createFromFormat('Y-m-d H:i:s', $ssDateTimeString);
    }


    public function getSSDateTimeFromCarbon(Carbon $carbonDate)
    {
        return $carbonDate->format('Y-m-d H:i:s');
    }
}
