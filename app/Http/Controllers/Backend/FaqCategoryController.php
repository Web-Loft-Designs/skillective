<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateFaqCategoryRequest;
use App\Http\Requests\UpdateFaqCategoryRequest;
use App\Repositories\FaqCategoryRepository;
use App\Repositories\FaqRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class FaqCategoryController extends AppBaseController
{
    /** @var  FaqCategoryRepository */
    private $faqCategoryRepository;

    public function __construct(FaqCategoryRepository $faqCategoryRepo)
    {
        $this->faqCategoryRepository = $faqCategoryRepo;
		parent::__construct();
    }

    /**
     * Display a listing of the FaqCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $faqCategories = $this->faqCategoryRepository->presentResponse($this->faqCategoryRepository->getFaqCategories($request));
        return view('backend.faqCategories.index', ['faqCategories' => $faqCategories, 'page_title'=>'FAQ Categories List']);
    }

    /**
     * Show the form for creating a new Faq.
     *
     * @return Response
     */
    public function create()
    {
        return view('backend.faqCategories.create', ['page_title'=>'Create FAQ Category']);
    }

    /**
     * Store a newly created Faq in storage.
     *
     * @param CreateFaqRequest $request
     *
     * @return Response
     */
    public function store(CreateFaqCategoryRequest $request)
    {
        $input = $request->all();

        $faq = $this->faqCategoryRepository->create($input);

        Flash::success('FAQ Category saved.');

        return redirect(route('backend.faq-categories.index'));
    }

    /**
     * Display the specified Faq.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $faq = $this->faqCategoryRepository->findWithoutFail($id);

        if (empty($faq)) {
            Flash::error('FAQ Category not found');

            return redirect(route('backend.faq-categories.index'));
        }

        return view('backend.faqCategories.show')->with('faq', $faq);
    }

    /**
     * Show the form for editing the specified Faq.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $faqCategory = $this->faqCategoryRepository->findWithoutFail($id);

        if (empty($faqCategory)) {
            Flash::error('FAQ Category not found');

            return redirect(route('backend.faq-categories.index'));
        }
		$vars = [
			'faqCategory' => $faqCategory,
			'page_title'=>'Edit FAQ Category'
		];
        return view('backend.faqCategories.edit', $vars);
    }

    /**
     * Update the specified Faq in storage.
     *
     * @param  int              $id
     * @param UpdateFaqRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFaqCategoryRequest $request)
    {
        $faq = $this->faqCategoryRepository->findWithoutFail($id);

        if (empty($faq)) {
            Flash::error('FAQ Category not found');

            return redirect(route('backend.faq-categories.index'));
        }

		$input = $request->all();
        $this->faqCategoryRepository->update($input, $id);

        Flash::success('FAQ Category updated.');

        return redirect(route('backend.faq-categories.index'));
    }

    /**
     * Remove the specified Faq from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $faq = $this->faqCategoryRepository->findWithoutFail($id);

        if (empty($faq)) {
            Flash::error('FAQ Category not found');

            return redirect(route('backend.faq-categories.index'));
        }

		$this->faqCategoryRepository->deleteOldImage($faq->image);
        $this->faqCategoryRepository->delete($id);

        Flash::success('FAQ category deleted successfully.');

        return redirect(route('backend.faq-categories.index'));
    }
}
