<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Debug extends Command
{
    protected $signature = 'debug';
    protected $description = 'Debuging';

    public function handle(){
        $this->debug();
    }

    public function debug()
    {
        //Получить минимальную, максимальную и среднюю стоимость всех рабов весом более 60 кг
        $sql = 'SELECT MAX(cost), MIN(cost), AVG(cost) FROM people WHERE weight_gram > 60000';
        $result = DB::select($sql);
        var_dump($result);

        //Выбрать категории, в которых больше 10 рабов.
        $sql = 'WITH p as (SELECT people.category_id
                       FROM people
                       GROUP BY category_id
                       HAVING COUNT(people.id) > 10)
            SELECT c.title, c.id
            FROM p
                     LEFT JOIN categories c on c.id = p.category_id';
        $result = DB::select($sql);
        var_dump($result);

        //Выбрать категорию с наибольшей суммарной стоимостью рабов.
        $sql = 'WITH c as (SELECT category_id, SUM(cost) as sum FROM people GROUP BY category_id),
                     c2 as (SELECT c.*, rank() over (order by sum desc) as r FROM c)
                SELECT category_id, sum
                from c2
                WHERE r = 1;';
        $result = DB::select($sql);
        var_dump($result);

        //Выбрать категории, в которых мужчин больше чем женщин.
        $sql = 'WITH p as (SELECT category_id,
                      SUM(CASE is_man
                              WHEN true THEN 1
                              WHEN false THEN -1
                              ELSE 0
                          END) as ratio
               FROM people
               GROUP BY category_id)
               SELECT category_id, categories.title
               FROM p
                    LEFT JOIN categories on categories.id = p.category_id
               WHERE p.ratio > 0';
        $result = DB::select($sql);
        var_dump($result);

        //Количество рабов в категории "Для кухни" (включая все вложенные категории).
        //Поле tree содержит массив из id родительских категорий
        $sql = 'SELECT COUNT(id) FROM people WHERE category_id IN (SELECT id FROM categories WHERE id = 1 OR tree[1] = 1)';
        $result = DB::select($sql);
        var_dump($result);
    }
}
