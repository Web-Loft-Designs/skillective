<?php

namespace App\Models;

use Eloquent as Model;
use Prettus\Repository\Contracts\Transformable;

class FaqCategory extends Model implements Transformable
{

	public $table = 'faq_categories';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	protected $dates = ['deleted_at'];

	public $fillable = [
		'title',
        'position'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'title' => 'string',
        'position' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'title' => 'required|unique:faq_categories',
        'position' => 'integer|nullable',
	];

    public function transform()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'count_faqs' => isset($this->count_faqs) ? $this->count_faqs : null
        ];
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\hasMany
	 **/
	public function faqs()
	{
		return $this->hasMany(\App\Models\Faq::class);
	}

	public static function boot() {
		parent::boot();

		static::deleting(function($category) {
			$category->faqs->each(function($faq) {
				$faq->faq_category_id = null;
				$faq->save();
			});
			return true;
		});
	}

    public function save(array $options = [])
    {
        if ($this->position==null){
            $this->position = 0;
        }

        parent::save($options);
    }
}
