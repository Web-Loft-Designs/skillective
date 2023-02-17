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
     * @return string
     */
    public function model()
    {
        return Testimonial::class;
    }

	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

    /**
     * @return string
     */
    public function presenter() {
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

    /**
     * @param $data
     * @return mixed
     */
    public function presentResponse($data){
		return $this->presenter->present($data);
	}
}
