<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Facades\ReportsBuilder;
use App\Exports\DemographicReportExport;
use App\Exports\GeographicReportExport;
use App\Exports\OtherReportsExport;

class ReportsController extends AppBaseController
{
	public function demographic(Request $request)
	{
		$groupedAgesCount = ReportsBuilder::getDemographicReportData();
		return view('backend.reports.demographic')
			->with('reportData', $groupedAgesCount);
	}

	public function demographicExport()
	{
		$groupedAgesCount = collect(ReportsBuilder::getDemographicReportData());
		return Excel::download(new DemographicReportExport($groupedAgesCount), 'demographic-report.csv');
	}

	public function geographics(Request $request)
	{
		$geographicReportData = ReportsBuilder::getGeographicReportData();

		return view('backend.reports.geographics')
			->with('reportData', $geographicReportData);
	}

	public function geographicsExport(Request $request)
	{
		$geographicReportData = collect(ReportsBuilder::getGeographicReportData());
		return Excel::download(new GeographicReportExport($geographicReportData), 'geographic-report.csv');
	}

	public function other(Request $request)
	{
		$otherReportData = ReportsBuilder::getOtherReportsData();

		return view('backend.reports.other')
			->with('reportData', $otherReportData);
	}

	public function otherExport(Request $request)
	{
		$otherReportData = collect(ReportsBuilder::getOtherReportsData());
		return Excel::download(new OtherReportsExport($otherReportData), 'other-reports.csv');
	}
}
