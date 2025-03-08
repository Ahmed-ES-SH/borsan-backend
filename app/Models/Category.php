<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = [
        'title_en',
        'title_ar',
        'image',
    ];
}
