<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageMeta extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'page_meta';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value'
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * @param Page $page
     * @param $meta_name
     * @param $meta_value
     * @return void
     */
    public static function updateMetaValue(Page $page, $meta_name, $meta_value){
        if ($page){
            $attributes = ['name'=>$meta_name, 'value'=>is_array($meta_value)?serialize($meta_value):$meta_value];
            $page_meta = self::findByPageAndName($page->id, $meta_name);
            if ($page_meta instanceof PageMeta){
                $page_meta->update($attributes);
            }else{
                $page->meta()->create($attributes);
            }
        }
    }

    /**
     * @param $page_id
     * @param $meta_name
     * @return mixed
     */
    public static function findByPageAndName($page_id, $meta_name){
        $meta = self::where([ ['name', '=', $meta_name], ['page_id', '=', $page_id] ])->first();
        return $meta;
    }
}
