<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;


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
     * @return BelongsTo
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    /**
     * @return array
     */
    public function transform()
    {
        return [
            'user_id' => $this->user_id,
            'message' => $this->message,
        ];
    }

    /**
     * @return array
     */
    public function toArray(){
        return $this->transform();
    }
}