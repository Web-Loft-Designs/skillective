<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Lesson;
use DB;
use Auth;
use Log;
use Illuminate\Http\Request;
use App\Repositories\LessonRepository;

class InstructorLessonsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
	private $lessonRepository		= null;
    private $request		= null;
    private $instructor_id	= null;
	private $local_time		= null;
	private $exportForAdmin = false;

    public function __construct(LessonRepository $lessonRepo, Request $request, $instructor_id, $local_time = false)
    {
        $this->request			= $request;
        $this->instructor_id	= $instructor_id;
		$this->local_time		= $local_time ? $local_time : time();
		$this->lessonRepository = $lessonRepo;
		$this->exportForAdmin = Auth::user()->hasRole(User::ROLE_ADMIN);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
        ];
    }

    public function headings() : array
    {
        $headings = ['ID'];

        if ($this->exportForAdmin){
            $headings = array_merge($headings, ['Instructor Name',
                'Instructor Address',
                'Instructor Mobile Phone',
                'Instructor Email']);
        }

        $headings = array_merge($headings, [
        	'Genre',
            'Date',
            'Total Spots Count',
            'Spot Price',
            'Location',
            'Description'
            ]);

        return $headings;
    }

	public function map($lesson): array
	{
		$map = [$lesson->id];

		if ($this->exportForAdmin){
			$map[] = $lesson->instructor->fullName();
			$map[] = $lesson->instructor->profile->getFullAddress();
			$map[] = $lesson->instructor->profile->mobile_phone;
			$map[] = $lesson->instructor->email;
		}
		$map[] = $lesson->genre->title;
		$map[] = $lesson->start; // Date::dateTimeToExcel(
		$map[] = $lesson->spots_count;
		$map[] = $lesson->spot_price;
		$map[] = $lesson->location;
		$map[] = $lesson->description;
        $map[] = $lesson->title;

		return $map;
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
//        $borders = Stats::_getPeriodBorders($this->period, $this->local_time);
		$this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
        return $this->lessonRepository->getInstructorLessons($this->request);
    }
}
