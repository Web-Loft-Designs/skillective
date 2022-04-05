<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Repositories\FaqCategoryRepository;
use App\Repositories\FaqRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class FaqController extends AppBaseController
{
    /** @var  FaqRepository */
    private $faqRepository;

    public function __construct(FaqRepository $faqRepo)
    {
        $this->faqRepository = $faqRepo;
		parent::__construct();
    }

    /**
     * Display a listing of the Faq.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $faqs = $this->faqRepository->presentResponse($this->faqRepository->getFaqs($request));

        return view('backend.faqs.index')
            ->with('faqs', $faqs);
    }

    /**
     * Show the form for creating a new Faq.
     *
     * @return Response
     */
    public function create( FaqCategoryRepository $categoryRepo )
    {
		$vars = [
			'categories' => $categoryRepo->all()
		];
        return view('backend.faqs.create', $vars);
    }

    /**
     * Store a newly created Faq in storage.
     *
     * @param CreateFaqRequest $request
     *
     * @return Response
     */
    public function store(CreateFaqRequest $request)
    {
        $input = $request->except(['file']);

        $faq = $this->faqRepository->create($input);

        if ($request->hasFile('file')) {
            $faq->updateAttachment( $request->file('file') );
        }

        Flash::success('FAQ saved.');

        return redirect(route('backend.faqs.index'));
    }

    /**
     * Display the specified Faq.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id, FaqCategoryRepository $categoryRepo)
    {
        $faq = $this->faqRepository->findWithoutFail($id);

        if (empty($faq)) {
            Flash::error('FAQ not found');

            return redirect(route('backend.faqs.index'));
        }
        $vars = [
            'categories' => $categoryRepo->all(),
            'faq' => $faq
        ];
        return view('backend.faqs.edit', $vars);
    }

    /**
     * Show the form for editing the specified Faq.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, FaqCategoryRepository $categoryRepo)
    {
        $faq = $this->faqRepository->findWithoutFail($id);

        if (empty($faq)) {
            Flash::error('FAQ not found');

            return redirect(route('backend.faqs.index'));
        }
		$vars = [
			'categories' => $categoryRepo->all(),
			'faq' => $faq
		];
        return view('backend.faqs.edit', $vars);
    }

    /**
     * Update the specified Faq in storage.
     *
     * @param  int              $id
     * @param UpdateFaqRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFaqRequest $request)
    {
        $faq = $this->faqRepository->findWithoutFail($id);

        if (empty($faq)) {
            Flash::error('FAQ not found');

            return redirect(route('backend.faqs.index'));
        }

        if($request->has('remove_uploaded') && $request->remove_uploaded==1){
            $faq->removeAttachment( );
        }
		if ($request->hasFile('file')) {
			$faq->updateAttachment( $request->file('file') );
		}

		$input = $request->except(['file', 'remove_uploaded']);
        $this->faqRepository->update($input, $id);

        Flash::success('FAQ updated.');

        return redirect(route('backend.faqs.index'));
    }

    /**
     * Remove the specified Faq from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function delete($id)
    {
        $faq = $this->faqRepository->findWithoutFail($id);

        if (empty($faq)) {
            Flash::error('FAQ not found');

            return redirect(route('backend.faqs.index'));
        }

        $this->faqRepository->delete($id);

        Flash::success('FAQ deleted');

        return redirect(route('backend.faqs.index'));
    }
}
