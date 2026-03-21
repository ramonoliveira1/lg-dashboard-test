<?php

namespace App\Http\Controllers;

use App\Productivity;
use App\Services\ProductivityServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** @var ProductivityServiceInterface */
    private ProductivityServiceInterface $productivityService;

    public function __construct(ProductivityServiceInterface $productivityService)
    {
        $this->productivityService = $productivityService;
    }

    /**
     * Display the production efficiency dashboard.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        $selectedLine = $request->input('product_line');

        if ($selectedLine && !Productivity::isValidProductLine($selectedLine)) {
            $selectedLine = null;
        }

        $summary      = $this->productivityService->getSummary($selectedLine);
        $dailyRecords = $this->productivityService->getDailyRecords($selectedLine);
        $productLines = $this->productivityService->getProductLines();

        return view('dashboard.index', compact(
            'summary',
            'dailyRecords',
            'productLines',
            'selectedLine'
        ));
    }
}

