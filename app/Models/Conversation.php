<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'deleted_by',
        'participant_one_id',
        'participant_two_id',
    ];

    protected $casts = [
        'deleted_by' => 'array',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    public function participantOne()
    {
        return $this->belongsTo(User::class, 'participant_one_id');
    }

    public function participantTwo()
    {
        return $this->belongsTo(User::class, 'participant_two_id');
    }

    // ✅ تصحيح علاقة الحظر
    public function block()
    {
        return $this->hasOne(ConversationBlock::class, 'conversation_id');
    }

    // ✅ تحسين دالة التحقق من الحظر وإرجاع بيانات الحظر
    public function getIsBlockedAttribute()
    {
        return $this->block()->exists() ? $this->block : null;
    }
}
