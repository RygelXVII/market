<?php

namespace App\Service;

use App\Repository\PeopleRepository;
use Exception;
use Illuminate\Support\Carbon;

class RentService
{
    const MAX_COUNT_WORK_HOURS = 16;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new PeopleRepository();
    }


    /**
     * Эта функция вызывается из контролера.
     * $timeFrom и $timeTo проверяются на этапе валидации на формат времени и на условие $timeFrom < $timeTo
     *
     * @param  int  $idHuman  - id раба
     * @param  string  $timeFrom
     * @param  string  $timeTo
     *
     * @return void
     * @throws Exception
     */
    public function rent(int $idHuman, string $timeFrom, string $timeTo)
    {
        $idUser = auth()->user()->id;
        $isVIP = auth()->user()->is_vip;

        //Проверка занятости раба в период с $timeFrom по $timeTo
        $Job = $this->repository->checkJob($idHuman, $timeFrom, $timeTo);

        if ($Job) {
            if (!$isVIP) {
                throw new Exception('Вы не можете арендовать раба. Он занят в период: '.$Job->from.' - '.$Job->to);
            }
            if ($Job->isVIP) {
                throw new Exception(
                    'Вы не можете арендовать раба. Он занят VIP клиентом в период: '.$Job->from.' - '.$Job->to
                );
            }
        }

        $human = $this->repository->getHuman($idHuman);
        $timeFrom = Carbon::createFromFormat('Y-m-d H:i', $timeFrom);
        $timeTo = Carbon::createFromFormat('Y-m-d H:i', $timeTo);

        $price = $this->calculatePriceAndCheckRecycling($human->rental_rate, $timeFrom, $timeTo);
        $this->repository->payment($idUser, $price);
    }

    /**
     * Подсчет цены всего периода аренды с проверкой переработок.
     * По хорошему, проверка переработок должна быть на этапе валидации.
     *
     * @param  float  $rentalRate
     * @param  Carbon  $timeFrom
     * @param  Carbon  $timeTo
     *
     * @return int
     * @throws Exception
     */
    private function calculatePriceAndCheckRecycling(float $rentalRate, Carbon $timeFrom, Carbon $timeTo): int
    {
        $price = 0;
        $days = ((clone $timeFrom)->startOfDay())->diffInDays((clone $timeTo)->startOfDay());

        if ($days == 1) {
            $hours = (clone $timeFrom)->startOfHour()->diffInHours((clone $timeTo)->addHour()->addSeconds(-1));
            $this->checkWorkHours($hours);
            $price += $rentalRate * $hours;
        } else {
            if ($days > 2) {
                $price += $rentalRate * self::MAX_COUNT_WORK_HOURS * ($days - 2);
            }

            //Проверка начального дня
            $hours = (clone $timeFrom)->startOfHour()->diffInDays((clone $timeFrom)->addDay()->startOfDay());
            $this->checkWorkHours($hours);
            $price += $rentalRate * $hours;

            //Проверка конечного дня
            $hours = (clone $timeTo)->startOfDay()->diffInDays((clone $timeTo)->addHour()->addSeconds(-1));
            $this->checkWorkHours($hours);
            $price += $rentalRate * $hours;
        }

        return $price;
    }

    /**
     * Проверка допустимого времени работы и вывод ошибки
     *
     * @param  int  $hours
     *
     * @return void
     * @throws Exception
     */
    private function checkWorkHours(int $hours)
    {
        if ($hours > self::MAX_COUNT_WORK_HOURS) {
            throw new Exception('Нельзя работать больше: '.self::MAX_COUNT_WORK_HOURS.' часов');
        }
    }

}
