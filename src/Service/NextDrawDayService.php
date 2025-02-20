<?php

namespace App\Service;

use App\Repository\LogRepository;
use Carbon\Carbon;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class NextDrawDayService
{

    private $params;
    private $logRepository;

    public function __construct(ContainerBagInterface $params, private LogRepository $logRepositor)
    {
        $this->params           = $params;
        $this->logRepository    = $logRepositor;
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
    function nextLotteryDate(string $date, string $source): array
    { 
        $carbonDate = Carbon::parse($date, 'UTC');

        $carbonDate     = $carbonDate->setTimezone('Europe/Dublin');

        $dayOfWeek      = (($carbonDate->isWednesday() or $carbonDate->isSaturday()) && intval($carbonDate->format('H')) <= 20) ? $carbonDate->dayOfWeek : $carbonDate->dayOfWeek +1; 

        $nextLotteryDay = $this->getNextLotteryDay($dayOfWeek);
        $lotteryDay =  $nextLotteryDay >= 0 ? Carbon::parse($carbonDate)->next($nextLotteryDay)->setTime(20, 0, 0) : Carbon::parse($carbonDate)->setTime(20, 0, 0);
        
        $carbonDate = $carbonDate->setTimezone('UTC');
        $lotteryDay = $lotteryDay->setTimezone('UTC');

        $data['input']  = $carbonDate->toDateTime();
        $data['output'] = $lotteryDay->toDateTime();
        $data['source'] = $source;

        $this->logRepository->createLog($data);

        return [
            'data'      => $lotteryDay->format('Y-m-d h:i a'),
            'message'   => $this->getMessage($carbonDate, $lotteryDay)
        ];

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

    /**
     * @param Carbon $value
     * @return string
     */

     public function getMessage(Carbon $carbonDate, Carbon $lotteryDay): string{
        $carbonDate     = $carbonDate->setTimezone('Europe/Dublin');
        if(($carbonDate->isWednesday() or $carbonDate->isSaturday()) && intval($carbonDate->format('H')) <= 20){
            $message =  'The next draw date go to be today at: ' . $lotteryDay->format('h:i a'); 
        }else if($carbonDate->isTuesday() or $carbonDate->isFriday()){
            $message =  'The next draw date go to be tomorrow at: ' . $lotteryDay->format('h:i a');
        }else{
            $message =  'The next draw date go to be the next ' . $lotteryDay->dayName .' at: ' . $lotteryDay->format('h:i a');
        }
        return $message;
     }

}
