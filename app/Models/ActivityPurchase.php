<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPurchase extends Model
{
    protected $table = 'activity_purchase';
    protected $casts = [
        'items' => 'array'
    ];
}
