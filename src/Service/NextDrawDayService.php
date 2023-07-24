<?php

namespace App\Service;

use Carbon\Carbon;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class NextDrawDayService
{

    private $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @param string $value
     * @return array
     */
    public function validateSingleValue($value): array
    {
        $status = true;
        $message = '';

        if(!preg_match($this->params->get('app.atom_regex'), $value)){
            $message    .= 'Invalid DateTime format. Try again.';
            $status     = false;
        }

        return [
            'message'   => $message,
            'status'    => $status
        ];
    }

    /**
     * @param string $value
     * @return array
     */
    function nextLotteryDate(string $date): array
    { 
        $carbonDate = Carbon::parse($date, 'UTC');
        $nextLotteryDay = $this->getNextLotteryDay($carbonDate->dayOfWeek);
        $lotteryDay =  $nextLotteryDay >= 0 ? Carbon::parse($carbonDate)->next($nextLotteryDay)->setTime(20, 0, 0) : Carbon::parse($carbonDate)->setTime(20, 0, 0);
        $lotteryDay = $this->validateDaylightSavingTime($lotteryDay);

        if($carbonDate->isWednesday() or $carbonDate->isSaturday()){
            return [
                'data'      => $lotteryDay->format('Y-m-d h:i a'),
                'message'   => 'The next draw date go to be today at: ' . $lotteryDay->format('h:i a') 
            ];
        }else if($carbonDate->isTuesday() or $carbonDate->isFriday()){
            return [
                'data'      => $lotteryDay->format('Y-m-d h:i a'),
                'message'   => 'The next draw date go to be tomorrow at: ' . $lotteryDay->format('h:i a') 
            ];
        }else{
            return [
                'data'      => $lotteryDay->format('Y-m-d h:i a'),
                'message'   => 'The next draw date go to be the next ' . $lotteryDay->dayName .' at: ' . $lotteryDay->format('h:i a') 
            ];
        }

    }
    
    /**
     * Validate Daylight Saving Time (DST) for a given Carbon date and adjust it if necessary.
     *
     * @param Carbon $dateTime The Carbon date to validate and adjust for DST.
     * @return Carbon The validated and adjusted Carbon date.
     */
    public function validateDaylightSavingTime(Carbon $dateTime): Carbon
    {
        // Get the current offset of the provided time zone
        $currentOffset = $dateTime->offset;

        // Calculate the DST offset for the date in the Europe/Dublin time zone (UTC+1 during DST, UTC+0 outside DST)
        $dstOffset = $dateTime->copy()->subHour()->offset;

        // Check if the current offset matches the DST offset
        if ($currentOffset == $dstOffset) {
            $dateTime->subHour(); // Subtract one hour if it's during DST
        }
        
        return $dateTime;
    }


    /**
     * @param int $value
     * @return int
     */
    public function getNextLotteryDay(int $dayOfWeek): int{
        
        if ($dayOfWeek >= 0 && $dayOfWeek < 3) {
            $nextLotteryDay = 3;
        } elseif ($dayOfWeek >= 4 && $dayOfWeek <= 5) {
            $nextLotteryDay = 6;
        } else {
            $nextLotteryDay = -1;
        }
        return $nextLotteryDay;
    }
}
