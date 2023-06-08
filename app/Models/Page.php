<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    const HOME_PAGE_ID              = 1;
    const HELP_PAGE_ID           	= 2;
    const PRIVACYPOLICY_PAGE_ID     = 3;
    const TERMSCONDITIONS_PAGE_ID   = 4;
    const SITEMAP_PAGE_ID           = 5;
    const ABOUT_US_PAGE_ID          = 6;
    const INSTRUCTOR_DASHBOARD_PAGE_ID       = 11;
    const STUDENT_DASHBOARD_PAGE_ID          = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'title', 'content', 'parent_id', 'template'
    ];

    // parent Page

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Page::class , 'parent_id');
    }

    // loads only direct children - 1 level

    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function meta()
    {
        return $this->hasMany(PageMeta::class);
    }

    /**
     * @return array
     */
    public function getAllMeta()
    {
        $return = [];
        $allMeta = $this->meta;
        foreach ($allMeta as $oneMeta){
			$return[$oneMeta->name] = $this->prepareOutputMeta($oneMeta->value);
        }
        return $return;
    }

    /**
     * @param $metaname
     * @return mixed|null
     */
    public function getMetaValue($metaname)
    {
        $allMeta = $this->meta;
        foreach ($allMeta as $oneMeta){
            if ($metaname==$oneMeta->name)
                return $this->prepareOutputMeta($oneMeta->value);
        }
        return null;
    }

    /**
     * @param $metaValue
     * @return mixed
     */
    private function prepareOutputMeta($metaValue){
		if ((strpos($metaValue, 'a:')===0)){
			try{
				return unserialize($metaValue);
			}catch (\Exception $e){
				return $metaValue;
			}
		}else{
			return $metaValue;
		}
	}

    /**
     * @param $metaname
     * @return false|string
     */
    public function getMetaValueYoutubeVideoId($metaname){
        $metavalue = $this->getMetaValue($metaname);
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $metavalue, $match)) {
            return $match[1];
        }
        else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public static function getTree(){
        $tree = self::whereNull('parent_id')->with('children')->get();
        return $tree;
    }

    /**
     * @return int[]
     */
    public static function getSpecialPages(){
        return array(
            'HOME_PAGE_ID'      		=>self::HOME_PAGE_ID,
            'HELP_PAGE_ID'       		=>self::HELP_PAGE_ID,
            'PRIVACYPOLICY_PAGE_ID'     => self::PRIVACYPOLICY_PAGE_ID,
            'TERMSCONDITIONS_PAGE_ID'   => self::TERMSCONDITIONS_PAGE_ID,
            'SITEMAP_PAGE_ID'           => self::SITEMAP_PAGE_ID,
            'ABOUT_US_PAGE_ID'          => self::ABOUT_US_PAGE_ID,
            'INSTRUCTOR_DASHBOARD_PAGE_ID'          => self::INSTRUCTOR_DASHBOARD_PAGE_ID,
            'STUDENT_DASHBOARD_PAGE_ID'          => self::STUDENT_DASHBOARD_PAGE_ID,
        );
    }

    /**
     * @param $filter
     * @param $per_page
     * @return mixed
     */
    public static function getList($filter = array(), $per_page = -1){
        $list = self::orderBy('title', 'ASC');

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

        // since all sport data is encrypted we cant use usual sql  search
        $conditions = [];
        $filterParams = ['title', 'name'];
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
