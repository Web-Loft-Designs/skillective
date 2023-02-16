<?php

namespace App\Repositories;

use App\Models\Profile;


class StudentAddClientListRepository extends BaseRepository
{
    public function model()
    {
        return Profile::class;
    }
}
