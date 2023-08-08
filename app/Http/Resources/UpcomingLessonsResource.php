<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpcomingLessonsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int)$this->id,
            'instructor_id' => $this->instructor_id,
            'genre_id'=> $this->genre_id,
            'genre'=> $this->genre->transform(),
            'students' => [],
            'instructor'=> [
                'id' => $this->instructor->id,
                'first_name' => $this->instructor->first_name,
                'last_name'=> $this->instructor->last_name,
                'full_name'=> $this->instructor->getName(),
                'profile' => [
                    'id' =>  $this->instructor->profile->id,
                    'user_id' => $this->instructor->profile->user_id,
                    'instagram_handle' => $this->instructor->profile->instagram_handle,
                    'image' => $this->instructor->profile->getImageUrl(),
                ]
            ],
            'start'=> $this->start,
            'end'=> $this->end,
            'timezone_id' => getTimezoneAbbrev($this->timezone_id),
            'timezone_id_name' => $this->timezone_id,
            'spots_count'=> $this->spots_count,
            'count_booked'=> (int)$this->count_booked,
            'spot_price'=> $this->spot_price,
            'location'=> $this->location,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'city' => $this->city,
            'state' => $this->state,
            'address' => $this->address,
            'zip' => $this->zip,
            'description' => $this->description,
            'lesson_type' => $this->lesson_type,
            'room_sid' => $this->room_sid,
            'room_completed' => $this->room_completed,
            'topic' => $this->topic,
            'preview' => $this->getPreviewUrl(),
            'title' => $this->title,
        ];
    }
}