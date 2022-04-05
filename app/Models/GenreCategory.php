<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;

class GenreCategory extends Model implements Transformable
{
    use SoftDeletes;

	public $table = 'genre_categories';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	protected $dates = ['deleted_at'];

	public $fillable = [
		'title'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'title' => 'string'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'title' => 'required|unique:genre_categories',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\hasMany
	 **/
	public function genres()
	{
		return $this->hasMany(\App\Models\Genre::class);
	}

	public function transform()
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'count_genres' => isset($this->count_genres) ? $this->count_genres : null
		];
	}

	public  static function boot() {
		parent::boot();

		static::deleting(function($category) {
			$category->genres->each(function($genre) {
				$genre->genre_category_id = null;
				$genre->save();
			});
			return true;
		});
	}
}
