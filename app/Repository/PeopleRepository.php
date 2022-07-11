<?php

namespace App\Repository;

use App\Jobs\Job;
use App\Models\People;
use App\Models\User;

class PeopleRepository
{

    /**
     * Получение информации о сдаваемом в аренду человек
     * @param $idPeople
     *
     * @return People
     */
    public function getHuman(int $idPeople): People
    {
        return People::find($idPeople);
    }

    /**
     * Оплата раба
     * @param  int  $idUser
     * @param  float  $amount
     */
    public function payment(int $idUser, float $amount)
    {
        DB::transaction(function () use ($idUser, $amount) {
            //Снимаем деньги со счета пользователя
            User::query()
                ->where('id', $idUser)
                ->decrement('balance', $amount);
            //todo добавляем запись о снятие денег в историю,
            //добавляем деньги на счет владельца раба, с учетом комиссии
        });
    }

    /**
     * Проверка занятости раба в период с $timeFrom по $timeTo
     *
     * @param $idHuman
     * @param $timeFrom
     * @param $timeTo
     *
     * @return Job|null - Если в периоде есть занятость то возвращает объект с пересечениями дат и информацией о арендаторе,
     */
    public function checkJob($idHuman, $timeFrom, $timeTo): Job|null
    {
        //
    }

}
