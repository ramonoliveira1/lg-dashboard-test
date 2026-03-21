<?php

namespace App\Repositories;

use App\Productivity;
use Illuminate\Support\Collection;

class ProductivityRepository implements ProductivityRepositoryInterface
{
    public function getSummary(string $plant, int $year, int $month, ?string $productLine = null): Collection
    {
        return Productivity::query()
            ->byPlant($plant)
            ->byMonth($year, $month)
            ->byProductLine($productLine)
            ->selectRaw('
                product_line,
                SUM(produced_quantity) AS total_produced,
                SUM(defect_quantity)   AS total_defects
            ')
            ->groupBy('product_line')
            ->orderBy('product_line')
            ->get()
            ->map(function ($row) {
                $row->produced_quantity = (int) $row->total_produced;
                $row->defect_quantity   = (int) $row->total_defects;

                return $row;
            });
    }

    public function getDailyRecords(string $plant, int $year, int $month, ?string $productLine = null): Collection
    {
        return Productivity::query()
            ->byPlant($plant)
            ->byMonth($year, $month)
            ->byProductLine($productLine)
            ->orderBy('production_date')
            ->orderBy('product_line')
            ->get();
    }
}

