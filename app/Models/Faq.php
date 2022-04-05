<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\Storage;
use File;
use Image;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Prettus\Repository\Contracts\Transformable;

class Faq extends Model implements HasMedia , Transformable
{
    use HasMediaTrait;

    public $table = 'faqs';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'title',
        'content',
		'faq_category_id',
        'video_url',
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
        'content' => 'string',
		'faq_category_id' => 'integer',
		'position' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required',
        'position' => 'integer|nullable',
        'faq_category_id' => 'required',
        'file' => 'nullable|mimes:mp4,mov,qt,jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
        'video_url' => 'nullable|regex:/^(https?:\/\/)/'
    ];

    public function transform()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'faq_category_id' => $this->faq_category_id,
            'video_url' => $this->video_url,
            'file' => $this->getAttachment(),
			'category' => $this->category ? $this->category->transform() : ['id'=>0, 'title'=>'Uncategorized'],
        ];
    }

    public function toArray(){
        return $this->transform();
    }

	public function category()
	{
		return $this->belongsTo(\App\Models\FaqCategory::class, 'faq_category_id');
	}

	public function getAttachment(){
        return $this->getMedia('attachments')->first();
    }

	public function updateAttachment($file){

        $this->removeAttachment();

        $this->addMedia($file)->toMediaCollection('attachments');

		return null;
	}

    public function removeAttachment(){
        foreach ($this->getMedia('attachments') as $media)
            $media->delete();
        return null;
    }

    public function save(array $options = [])
    {
        if ($this->position==null){
            $this->position = 0;
        }

        parent::save($options);
    }
}
