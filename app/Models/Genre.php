<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use File;
use Image;
use Prettus\Repository\Contracts\Transformable;

/**
 * Class Genre
 * @package App\Models
 * @version July 22, 2019, 12:41 pm UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection 
 * @property \Illuminate\Database\Eloquent\Collection users
 * @property string title
 * @property string image
 * @property boolean is_featured
 */
class Genre extends Model implements Transformable
{
    use SoftDeletes;

    public $table = 'genres';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

	const IMAGES_PATH = 'genres/';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'title',
        'image',
		'genre_category_id',
        'is_featured'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'image' => 'string',
        'is_featured' => 'boolean',
		'genre_category_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|unique:genres',
        'image' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_genre');
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 **/
	public function lessons()
	{
		return $this->hasMany(\App\Models\Lesson::class);
	}

	public function category()
	{
		return $this->belongsTo(\App\Models\GenreCategory::class, 'genre_category_id');
	}

	public function transform()
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'genre_category_id' => $this->genre_category_id,
			'image' => $this->getImageUrl(),
//			'category' => $this->category ? $this->category->transform() : ['id'=>0, 'title'=>'Uncategorized'],
		];
	}

	public function toArray(){
		return $this->transform();
	}

	public function getImageUrl(){
		if ($this->image)
			return '/storage/' . self::IMAGES_PATH . $this->image;
		else
			return config('app.url') . Setting::getValue('default_genre_image');
	}

	public function uploadImage($image){
		$destination = storage_path('app/public/' . self::IMAGES_PATH);
		$ext = $image->getClientOriginalExtension();
		$baseName = str_replace('.'.$ext, '', $image->getClientOriginalName());
		$fileName  = $baseName . '.' . $ext;
		$index = 1;

		while (File::exists($destination.$fileName)){
			$fileName = $baseName . '-'.$index . '.' . $ext;
			$index++;
		}

		$img = Image::make($image->getRealPath());
		$img->resize(200, 200, function ($constraint) {
			$constraint->aspectRatio();
		});

		$img->stream();

		Storage::disk('public')->put(self::IMAGES_PATH.$fileName, $img);

		$this->update(['image'=>$fileName]);

		return $fileName;
	}

	public function deleteOldImage(){
		$destination = storage_path('app/public/' . self::IMAGES_PATH);
		// remove the old image
		if ($this->image!= '' && File::exists($destination.$this->image)) {
			unlink($destination.$this->image);
			return true;
		}
		return false;
	}
}
