<?php

namespace App\Services;

use App\Productivity;
use App\Repositories\ProductivityRepositoryInterface;
use Illuminate\Support\Collection;

class ProductivityService implements ProductivityServiceInterface
{
    /** @var ProductivityRepositoryInterface */
    private ProductivityRepositoryInterface $repository;

    public function __construct(ProductivityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Returns aggregated production summary for January 2026,
     * optionally filtered by product line.
     *
     * @param  string|null  $productLine
     * @return Collection
     */
    public function getSummary(?string $productLine = null): Collection
    {
        return $this->repository->getSummary('Planta A', 2026, 1, $productLine);
    }

    /**
     * Returns all daily records for January 2026,
     * optionally filtered by product line.
     *
     * @param  string|null  $productLine
     * @return Collection
     */
    public function getDailyRecords(?string $productLine = null): Collection
    {
        return $this->repository->getDailyRecords('Planta A', 2026, 1, $productLine);
    }

    /**
     * Returns all valid product lines from the Model constants.
     *
     * @return array
     */
    public function getProductLines(): array
    {
        return Productivity::PRODUCT_LINES;
    }

    /**
     * Calculates efficiency percentage.
     *
     * @param  int  $produced
     * @param  int  $defects
     * @return float
     */
    public function calculateEfficiency(int $produced, int $defects): float
    {
        if ($produced === 0) {
            return 0.0;
        }

        return round((($produced - $defects) / $produced) * 100, 2);
    }
}

