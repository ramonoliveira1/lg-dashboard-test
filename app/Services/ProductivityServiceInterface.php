<?php

namespace App\Services;

use Illuminate\Support\Collection;

interface ProductivityServiceInterface
{
    /**
     * Returns aggregated production summary, optionally filtered by product line.
     *
     * @param  string|null  $productLine
     * @return Collection
     */
    public function getSummary(?string $productLine = null): Collection;

    /**
     * Returns all daily production records, optionally filtered by product line.
     *
     * @param  string|null  $productLine
     * @return Collection
     */
    public function getDailyRecords(?string $productLine = null): Collection;

    /**
     * Returns all valid product lines.
     *
     * @return array
     */
    public function getProductLines(): array;

    /**
     * Calculates efficiency percentage.
     *
     * @param  int  $produced
     * @param  int  $defects
     * @return float
     */
    public function calculateEfficiency(int $produced, int $defects): float;
}

