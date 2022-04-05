<?php

namespace App\Repositories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Common\BaseRepository;
use Prettus\Validator\Exceptions\ValidatorException;

class StudentAddClientListRepository extends BaseRepository
{
    public function model()
    {
        return Profile::class;
    }
}
