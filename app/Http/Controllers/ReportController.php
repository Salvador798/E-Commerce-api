<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function salesPerDay()
    {
        return ReportResource::collection($this->reportService->salesPerDay());
    }

    public function salesByCategory()
    {
        return ReportResource::collection($this->reportService->salesByCategory());
    }

    public function salesPerCustomer()
    {
        return ReportResource::collection($this->reportService->salesPerCustomer());
    }

    public function salesPerProduct()
    {
        return ReportResource::collection($this->reportService->salesPerProduct());
    }

    public function salesByRank(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'until' => 'required|date'
        ]);

        return ReportResource::collection($this->reportService->salesByRank($request->from, $request->until));
    }

    public function salesByPaymentMethod()
    {
        return ReportResource::collection($this->reportService->salesByPaymentMethod());
    }

    public function salesByStatusOrder()
    {
        return ReportResource::collection($this->reportService->salesByStatusOrder());
    }

    public function salesByRegion()
    {
        return ReportResource::collection($this->reportService->salesByRegion());
    }
}
