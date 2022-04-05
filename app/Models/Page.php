<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PageMeta;

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
    public function parent()
    {
        return $this->belongsTo('App\Models\Page' , 'parent_id');
    }

    // loads only direct children - 1 level
    public function children()
    {
        return $this->hasMany('App\Models\Page', 'parent_id', 'id');
    }

    public function meta()
    {
        return $this->hasMany(PageMeta::class);
    }

    public function getAllMeta()
    {
        $return = [];
        $allMeta = $this->meta;
        foreach ($allMeta as $oneMeta){
			$return[$oneMeta->name] = $this->prepareOutputMeta($oneMeta->value);
        }
        return $return;
    }

    public function getMetaValue($metaname)
    {
        $allMeta = $this->meta;
        foreach ($allMeta as $oneMeta){
            if ($metaname==$oneMeta->name)
                return $this->prepareOutputMeta($oneMeta->value);
        }
        return null;
    }

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

    public function getMetaValueYoutubeVideoId($metaname){
        $metavalue = $this->getMetaValue($metaname);
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $metavalue, $match)) {
            return $match[1];
        }
        else {
            return false;
        }
    }

    public static function getTree(){
        $tree = self::whereNull('parent_id')->with('children')->get();

        return $tree;
//        $tree = array();
//
//        foreach ( $all as &$s ) {
//            if ( is_null($s->parent_id) ) {
//                // no parent_id so we put it in the root of the array
//                $tree[] = &$s;
//            }
//            else {
//                $pid = $s['parent_id'];
//                if ( isset($source[$pid]) ) {
//                    // If the parent ID exists in the source array
//                    // we add it to the 'children' array of the parent after initializing it.
//
//                    if ( !isset($source[$pid]['children']) ) {
//                        $source[$pid]['children'] = array();
//                    }
//
//                    $source[$pid]['children'][] = &$s;
//                }
//            }
//        }
//        return $tree;
    }

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
