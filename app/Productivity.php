<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Productivity extends Model
{
    /**
     * Product line constants — single source of truth.
     */
    const LINE_GELADEIRA        = 'Geladeira';
    const LINE_MAQUINA_DE_LAVAR = 'Máquina de Lavar';
    const LINE_TV               = 'TV';
    const LINE_AR_CONDICIONADO  = 'Ar-Condicionado';

    /**
     * All valid product lines.
     */
    const PRODUCT_LINES = [
        self::LINE_GELADEIRA,
        self::LINE_MAQUINA_DE_LAVAR,
        self::LINE_TV,
        self::LINE_AR_CONDICIONADO,
    ];

    protected $fillable = [
        'plant',
        'product_line',
        'produced_quantity',
        'defect_quantity',
        'production_date',
    ];

    protected $casts = [
        'produced_quantity' => 'integer',
        'defect_quantity'   => 'integer',
        'production_date'   => 'date',
    ];

    /**
     * Appended attributes (included in JSON/array serialization).
     */
    protected $appends = ['efficiency'];

    /**
     * Accessor: efficiency (%) = (produced - defects) / produced * 100
     */
    public function getEfficiencyAttribute(): float
    {
        if (empty($this->produced_quantity)) {
            return 0.0;
        }

        return round(
            (($this->produced_quantity - $this->defect_quantity) / $this->produced_quantity) * 100,
            2
        );
    }

    /**
     * Check if a given string is a valid product line.
     */
    public static function isValidProductLine(string $line): bool
    {
        return in_array($line, self::PRODUCT_LINES, true);
    }

    public function scopeByProductLine(Builder $query, ?string $productLine): Builder
    {
        if ($productLine && self::isValidProductLine($productLine)) {
            return $query->where('product_line', $productLine);
        }

        return $query;
    }

    public function scopeByPlant(Builder $query, string $plant): Builder
    {
        return $query->where('plant', $plant);
    }

    public function scopeByMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('production_date', $year)
                     ->whereMonth('production_date', $month);
    }
}
