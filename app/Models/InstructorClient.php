<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InstructorClient extends Pivot
{
    protected $fillable = [
    	'instructor_id',
		'client_id'
    ];

    protected $table = 'instructor_client';
}
