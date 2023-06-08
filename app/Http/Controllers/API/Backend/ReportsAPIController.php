<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Facades\ReportsBuilder;

class ReportsAPIController extends AppBaseController
{
    /**
     * @return JsonResponse
     */
    public function demographic()
	{
		$groupedAgesCount = ReportsBuilder::getDemographicReportData();
		return $this->sendResponse($groupedAgesCount);
	}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function geographics(Request $request)
	{
		$geographicReportData = ReportsBuilder::getGeographicReportData();
		return $this->sendResponse($geographicReportData);
	}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function other(Request $request)
	{
		$otherReportData = ReportsBuilder::getOtherReportsData();
		return $this->sendResponse($otherReportData);
	}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function overview(Request $request)
	{
		$selectedPeriod = $request->input('period', null);
		$overviewWidgetData = ReportsBuilder::getOverview($selectedPeriod);
		return $this->sendResponse($overviewWidgetData);
	}
}
