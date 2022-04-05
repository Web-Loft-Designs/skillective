<?php

namespace App\Http\Controllers\API;

use App\Repositories\FaqRepository;
use App\Repositories\FaqCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FaqAPIController extends AppBaseController
{
    /** @var  FaqRepository */
    private $faqRepository;

	/** @var  FaqCategoryRepository */
	private $faqCategoryRepository;

    public function __construct(FaqRepository $faqRepo, FaqCategoryRepository $faqCategoryRepo)
    {
        $this->faqRepository = $faqRepo;
        $this->faqCategoryRepository = $faqCategoryRepo;
    }

    /**
     * Display a listing of the Faq.
     * GET|HEAD /faqs
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->faqRepository->pushCriteria(new RequestCriteria($request));
        $this->faqRepository->pushCriteria(new LimitOffsetCriteria($request));
        $faqs = $this->faqRepository->orderBy('position')->orderBy('title')->all();

        return $this->faqRepository->presentResponse($faqs)['data'];
    }

	public function categorizedFaqs(Request $request)
	{
		return $this->faqRepository->presentResponse($this->faqRepository->getCategorizedFaqs())['data'];
	}
}
