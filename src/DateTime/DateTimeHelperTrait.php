<?php
namespace TitleDK\Calendar\DateTime;

use Carbon\Carbon;

trait DateTimeHelperTrait
{
    /**
     * @param string $ssDateTimeString time returned from a SilverStripe DateField or DateTimeField
     *
     * @todo Timezones
     *
     * @return Carbon same time, but in Carbon
     */
    public function carbonDateTime($ssDateTimeString)
    {
        //2018-05-21 13:04:00
        $result = Carbon::createFromFormat('Y-m-d H:i:s', $ssDateTimeString);
        return $result;
    }

    /**
     * @param Carbon $carbonDate
     */
    public function getSSDateTimeFromCarbon($carbonDate)
    {
        $dateAsString = $carbonDate->format('Y-m-d H:i:s');
        return $dateAsString;
    }
}
