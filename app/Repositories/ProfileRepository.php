<?php

namespace App\Repositories;

use App\Models\Profile;
use InfyOm\Generator\Common\BaseRepository;
use File;
use Image;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProfileRepository
 * @package App\Repositories
 * @version July 22, 2019, 3:24 pm UTC
 *
 * @method Profile findWithoutFail($id, $columns = ['*'])
 * @method Profile find($id, $columns = ['*'])
 * @method Profile first($columns = ['*'])
*/
class ProfileRepository extends BaseRepository
{

	protected $skipPresenter = true;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'instagram_handle',
        'address',
        'city',
        'state',
        'zip',
        'mobile_phone',
        'dob',
        'about_me',
        'avatar'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Profile::class;
    }
}
