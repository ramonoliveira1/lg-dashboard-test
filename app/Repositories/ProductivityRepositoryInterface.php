<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface ProductivityRepositoryInterface
{
    /**
     * Returns aggregated production data grouped by product line.
     *
     * @param  string       $plant
     * @param  int          $year
     * @param  int          $month
     * @param  string|null  $productLine
     * @return Collection
     */
    public function getSummary(string $plant, int $year, int $month, ?string $productLine = null): Collection;

    /**
     * Returns all daily production records.
     *
     * @param  string       $plant
     * @param  int          $year
     * @param  int          $month
     * @param  string|null  $productLine
     * @return Collection
     */
    public function getDailyRecords(string $plant, int $year, int $month, ?string $productLine = null): Collection;
}

