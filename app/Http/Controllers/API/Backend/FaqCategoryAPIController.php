<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\FaqCategory;
use App\Repositories\FaqCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Exceptions\RepositoryException;

class FaqCategoryAPIController extends AppBaseController
{
    /** @var  FaqCategoryRepository */
    private $faqCategoryRepository;

    public function __construct(FaqCategoryRepository $faqCategoryRepo)
    {
        $this->faqCategoryRepository = $faqCategoryRepo;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
		$faqCategories = $this->faqCategoryRepository
            ->presentResponse( $this->faqCategoryRepository->getFaqCategories($request) );
        return $this->sendResponse($faqCategories);
    }

    /**
     * @param FaqCategory $category
     * @return JsonResponse
     */
    public function delete(FaqCategory $category)
	{
		$category->delete();
		return $this->sendResponse(true, 'Faq Category deleted');
	}
}