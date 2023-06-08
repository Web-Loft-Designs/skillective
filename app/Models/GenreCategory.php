<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @return HasMany
     */
    public function genres()
	{
		return $this->hasMany(Genre::class);
	}

    /**
     * @return array
     */
    public function transform()
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'count_genres' => isset($this->count_genres) ? $this->count_genres : null
		];
	}

    /**
     * @return void
     */
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
