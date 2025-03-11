<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'message',
        'attachment',
        'conversation_id',
        'sender_id',
    ];



    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
