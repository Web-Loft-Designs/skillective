<?php

namespace App\Http\Controllers\API;

use App\Repositories\FaqRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;


class FaqAPIController extends AppBaseController
{
    /** @var  FaqRepository */
    private $faqRepository;


    public function __construct(FaqRepository $faqRepo)
    {
        parent::__construct();
        $this->faqRepository = $faqRepo;
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
        $this->faqRepository->pushCriteria(new RequestCriteria($request));
        $this->faqRepository->pushCriteria(new LimitOffsetCriteria($request));
        $faqs = $this->faqRepository->orderBy('position')->orderBy('title')->all();

        return $this->faqRepository->presentResponse($faqs)['data'];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function categorizedFaqs(Request $request)
	{
		return $this->faqRepository->presentResponse($this->faqRepository->getCategorizedFaqs())['data'];
	}
}
