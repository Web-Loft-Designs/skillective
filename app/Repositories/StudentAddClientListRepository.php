<?php

namespace App\Repositories;

use App\Models\Profile;


class StudentAddClientListRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Profile::class;
    }
}
