<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Facades\ReportsBuilder;
use App\Exports\DemographicReportExport;

class ReportsAPIController extends AppBaseController
{
	public function demographic()
	{
		$groupedAgesCount = ReportsBuilder::getDemographicReportData();
		return $this->sendResponse($groupedAgesCount);
	}

	public function geographics(Request $request)
	{
		$geographicReportData = ReportsBuilder::getGeographicReportData();
		return $this->sendResponse($geographicReportData);
	}

	public function other(Request $request)
	{
		$otherReportData = ReportsBuilder::getOtherReportsData();
		return $this->sendResponse($otherReportData);
	}

	public function overview(Request $request)
	{
		$selectedPeriod = $request->input('period', null);
		$overviewWidgetData = ReportsBuilder::getOverview($selectedPeriod);
		return $this->sendResponse($overviewWidgetData);
	}
}
