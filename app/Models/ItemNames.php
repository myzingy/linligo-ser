<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemNames extends Model
{
    protected $table = 'item_names';
    protected $fillable = ['name', 'price'];
}
