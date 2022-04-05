<?php

namespace App\Repositories;

use App\Models\Genre;
use App\Models\GenreCategory;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use App\Criteria\GenreSearchCriteria;
use App\Criteria\GenreStatusCriteria;
use App\Criteria\GenreFilterByCategoryCriteria;
use Cookie;
use DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Class GenreRepository
 * @package App\Repositories
 * @version July 22, 2019, 12:41 pm UTC
 *
 * @method Genre findWithoutFail($id, $columns = ['*'])
 * @method Genre find($id, $columns = ['*'])
 * @method Genre first($columns = ['*'])
*/
class GenreRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'is_featured'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Genre::class;
    }

	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

	public function presenter() {
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

	public function getGenres(Request $request){
		$this->resetCriteria();
		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new GenreSearchCriteria($request->get('s')));

		$this->pushCriteria(new GenreStatusCriteria($request->get('status', 'active')));

		if ($request->has('category')){
			$this->pushCriteria(new GenreFilterByCategoryCriteria($request->get('category')));
		}

		$this->scopeQuery(function($query){
			return $query->orderBy('title','asc');
		});

		$defaultPerPage = 25;
		$perPage = Cookie::get('adminGenresPerPage', $defaultPerPage);

		$this->with(['category']);

		return $this->paginate($perPage, ['genres.*']);
	}

	// all genres for frontend
	public function getSiteGenres(){
		return $this->scopeQuery(function($query){
			return $query->orderBy('title','asc')->where('is_disabled', 0);
		})->with(['category'])->get();
	}

	public function getSiteLessonsGenres(){
		return $this->scopeQuery(function($query){
			$nowOnServer = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
			$in = DB::table('lessons')
					 ->select('lessons.genre_id')
					 ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
					 ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
					 ->groupBy('lessons.genre_id')
					 ->get()
					 ->pluck('genre_id')
					 ->all();

			$query->orderBy('title','asc')->where('is_disabled', 0);
			if (count($in))
				$query->whereIn('id', $in);
			return $query;
		})->with(['category'])->get();
	}

    public function getSiteInstructorsGenres(){

        return $this->scopeQuery(function($query){
            $instructorRoleId = Role::findByName(User::ROLE_INSTRUCTOR)->id;
            $in = DB::table('users')
                ->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
                ->leftJoin("user_genre", 'users.id', '=', "user_genre.user_id")
                ->where('model_has_roles.role_id', '=', $instructorRoleId)
                ->where('model_type', '=', 'App\Models\User')
                ->groupBy('user_genre.genre_id')
                ->get()
                ->pluck('genre_id')
                ->all();

            $query->orderBy('title','asc')->where('is_disabled', 0);
            if (count($in))
                $query->whereIn('id', $in);
            return $query;
        })->with(['category'])->get();
    }

    public function getFeatured($limit = null){

		$this->resetCriteria();
		if ($limit>0){
			$request = new Request([
				'limit'   => $limit
			]);
			$this->pushCriteria(new LimitOffsetCriteria($request));
		}

    	return $this->with(['category'])->orderBy('genres.title', 'asc')->findByField('is_featured', 1)->where('is_disabled', 0);
	}

	public function getCategorizedGenres(){
		$this->scopeQuery(function($query){
			return $query->orderBy('title','asc')->where('is_disabled', 0);
		});

		$categorized = [];
		$genres = $this->with('category')->all();
		foreach($genres as $genre){
			$category = $genre->category? $genre->category->title : 'Uncategorized';
			if (!isset($categorized[$category]))
				$categorized[$category] = [];
			$categorized[$category][] = $genre->transform();
		}
		ksort($categorized);
		return $categorized;
	}

	public function presentResponse($data){
		return $this->presenter->present($data);
	}
}
