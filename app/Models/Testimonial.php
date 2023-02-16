<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Testimonial extends Model implements HasMedia //, Transformable
{
    use InteractsWithMedia;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'instagram_handle', 'content', 'position'
    ];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'name' => 'required|max:255',
		'content' => 'required',
		'image'=> 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
	];


	public function registerMediaConversions(Media $media = null): void
	{
		$this->addMediaConversion('circle_image')
			 ->fit(Manipulations::FIT_CROP, 40, 40)
			 ->nonQueued()
			 ->performOnCollections('testimonial');
	}

	public function uploadImage($requestImage)
	{
		try{
			if ($requestImage instanceof \Illuminate\Http\UploadedFile) {
				$this->addMedia( $requestImage )->toMediaCollection( 'testimonial' );
				return true;
			}
		}catch (\Exception $e){
			Log::error($e->getMessage());
			return false;
		}
		return false;
	}

	public function getImageUrl(){
		$testimonialImage = $this->media()
        ->whereIn('collection_name', ['testimonial'])
        ->orderBy('order_column', 'DESC')
        ->first();
		if ($testimonialImage) {
            return $testimonialImage->getFullUrl('circle_image');
        }
		return null;
	}

    public static function getList($filter = array(), $per_page = -1){
        $list = self::orderBy('created_at', 'DESC');

        $date_filter_field = 'created_at';
        $created_at_from    = ( isset($filter['created_at_from']) && preg_match('/\d{2}\/\d{2}\/\d{4}/', $filter['created_at_from'])) ? $filter['created_at_from'] : null;
        $created_at_to      = (isset($filter['created_at_to']) && preg_match('/\d{2}\/\d{2}\/\d{4}/', $filter['created_at_to'])) ? $filter['created_at_to'] : null;
        if ($created_at_from!=null || $created_at_to!=null){
            if ( $created_at_from!=null && $created_at_to!=null) {
                if ($created_at_from!=$created_at_to)
                    $list = $list->whereRaw( " DATE($date_filter_field) BETWEEN '" . date('Y-m-d', strtotime($created_at_from)) . "' AND '" . date('Y-m-d', strtotime($created_at_to)) . "'");
                else
                    $list = $list->whereRaw( " DATE($date_filter_field) = '" . date('Y-m-d', strtotime($created_at_from)) . "'");
            }elseif($created_at_from!=null){
                $list = $list->whereRaw( " DATE($date_filter_field) >= '" . date('Y-m-d', strtotime($created_at_from)) . "'" );
            }elseif($created_at_to!=null){
                $list = $list->whereRaw( " DATE($date_filter_field) <= '" . date('Y-m-d', strtotime($created_at_to)) . "'" );
            }
            if( $created_at_from!=null ){
                $filterValues['created_at_from'] = $created_at_from;
                $sortVars['created_at_from'] = $created_at_from;
            }
            if( $created_at_to!=null ){
                $filterValues['created_at_to'] = $created_at_to;
                $sortVars['created_at_to'] = $created_at_to;
            }
        }


        $filterParams = ['name', 'company'];
        foreach ($filterParams as $paramName){
            if (isset($filter[$paramName]) && $filter[$paramName]!=''){
                $val = '%'.$filter[$paramName].'%';
                $list = $list->where($paramName, 'LIKE', $val);
            }
        }

        if ($per_page>=0)
            return $list->paginate($per_page);
        else
            return $list->get();
    }
}
