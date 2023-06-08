<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Faq;
use App\Repositories\FaqRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Exceptions\RepositoryException;

class FaqAPIController extends AppBaseController
{
    /** @var  FaqRepository */
    private $faqRepository;

    public function __construct(FaqRepository $faqRepo)
    {
        $this->faqRepository = $faqRepo;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
		$faqs = $this->faqRepository->presentResponse( $this->faqRepository->getFaqs($request) );

        return $this->sendResponse($faqs);
    }

    /**
     * @param Faq $faq
     * @return JsonResponse
     */
    public function delete(Faq $faq)
	{
		$faq->delete();
		return $this->sendResponse(true, 'FAQ deleted');
	}
}
