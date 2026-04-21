<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
    'type',
    'address1',
    'address2',
    'city',
    'state',
    'zipcode',
    'isMain',
    'country_code',
    'user_id',
];

public function user()
{
    return $this->belongsTo(User::class);
}

}
