<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\FaqCategory;
use App\Repositories\FaqCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;


class FaqCategoryAPIController extends AppBaseController
{
    /** @var  FaqCategoryRepository */
    private $faqCategoryRepository;

    public function __construct(FaqCategoryRepository $faqCategoryRepo)
    {
        $this->faqCategoryRepository = $faqCategoryRepo;
    }

    public function index(Request $request)
    {
		$faqCategories = $this->faqCategoryRepository->presentResponse( $this->faqCategoryRepository->getFaqCategories($request) );

        return $this->sendResponse($faqCategories);
    }

	public function delete(FaqCategory $category)
	{
		$category->delete();
		return $this->sendResponse(true, 'Faq Category deleted');
	}
}