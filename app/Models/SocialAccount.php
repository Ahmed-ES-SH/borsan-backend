<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'whatsapp_number',
        'gmail_account',
        'facebook_account',
        'x_account',
        'youtube_account',
        'instgram_account',
        'snapchat_account',
    ];
}
