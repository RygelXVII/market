<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;


/**
 * @property string $name
 * @property boolean $is_man
 * @property string $birthday
 * @property integer $weight_gram
 * @property integer $skin_id
 * @property integer $location_id
 * @property string $description
 * @property float $rental_rate
 * @property float $cost
 */
class People extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_man',
        'birthday',
        'weight_gram',
        'skin_id',
        'location_id',
        'description',
        'rental_rate',
        'cost',
    ];
}
