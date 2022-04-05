<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Faq;
use App\Repositories\FaqRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;


class FaqAPIController extends AppBaseController
{
    /** @var  FaqRepository */
    private $faqRepository;

    public function __construct(FaqRepository $faqRepo)
    {
        $this->faqRepository = $faqRepo;
    }

    public function index(Request $request)
    {
		$faqs = $this->faqRepository->presentResponse( $this->faqRepository->getFaqs($request) );

        return $this->sendResponse($faqs);
    }

	public function delete(Faq $faq)
	{
		$faq->delete();
		return $this->sendResponse(true, 'FAQ deleted');
	}
}
