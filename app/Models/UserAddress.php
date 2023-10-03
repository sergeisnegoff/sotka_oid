<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $city
 * @property int|null $city_id
 * @property string $address
 * @property int|null $address_id
 * @property string $region
 * @property int|null $region_id
 * @property string $house
 * @property Carbon $created_at
 * @property int $updated_at
 */
class UserAddress extends Model {
    protected $table = 'user_address';
}
