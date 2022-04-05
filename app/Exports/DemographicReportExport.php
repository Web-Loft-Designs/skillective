<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use DB;
use Log;
use Illuminate\Http\Request;

class DemographicReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $reportDataCollection = null;

    public function __construct($reportDataCollection)
    {
        $this->reportDataCollection = $reportDataCollection;
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
        $headings = ['Age', 'Percent', 'Users number'];

        return $headings;
    }

	public function map($row): array
	{
		$map = [
			$row['range'],
			(!$row['percent'] ? 0 : $row['percent']) . '%',
			!$row['count'] ? 0 : $row['count'],
		];
		return $map;
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->reportDataCollection;
    }
}
