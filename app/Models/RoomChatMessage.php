<?php

namespace App\Models;

use Eloquent as Model;
use Prettus\Repository\Contracts\Transformable;
use Auth;

class RoomChatMessage extends Model implements Transformable
{
    public $table = 'room_chat_messages';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'user_id',
        'lesson_id',
        'message'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function lesson()
    {
        return $this->belongsTo(\App\Models\Lesson::class, 'lesson_id');
    }

    public function transform()
    {
        return [
            'user_id' => $this->user_id,
            'message' => $this->message,
        ];
    }

    public function toArray(){
        return $this->transform();
    }
}