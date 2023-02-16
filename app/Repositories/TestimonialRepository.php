<?php

namespace App\Repositories;

use App\Models\Testimonial;


/**
 * Class TestimonialRepository
 * @package App\Repositories
 * @version July 22, 2019, 12:41 pm UTC
 *
 * @method Testimonial findWithoutFail($id, $columns = ['*'])
 * @method Testimonial find($id, $columns = ['*'])
 * @method Testimonial first($columns = ['*'])
*/
class TestimonialRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
		'name', 'instagram_handle', 'content'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Testimonial::class;
    }

	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

	public function presenter() {
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

	public function presentResponse($data){
		return $this->presenter->present($data);
	}
}
