<?php

use App\Productivity;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductivitySeeder extends Seeder
{
    /**
     * Seed production data for Planta A – January 2026.
     * One record per product line per working day (Mon–Sat).
     */
    public function run()
    {
        DB::table('productivities')->truncate();

        $plant = 'Planta A';

        // Base daily production targets per line (using Model constants)
        $lines = [
            Productivity::LINE_GELADEIRA        => ['base' => 320, 'defect_rate' => 0.025],
            Productivity::LINE_MAQUINA_DE_LAVAR => ['base' => 280, 'defect_rate' => 0.035],
            Productivity::LINE_TV               => ['base' => 500, 'defect_rate' => 0.018],
            Productivity::LINE_AR_CONDICIONADO  => ['base' => 210, 'defect_rate' => 0.048],
        ];

        $records = [];
        $now     = now();

        for ($day = 1; $day <= 31; $day++) {
            $date = Carbon::create(2026, 1, $day);

            // Skip Sundays
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            foreach ($lines as $lineName => $config) {
                $variation = rand(-15, 20);
                $produced  = max(1, $config['base'] + $variation);

                $defectVariance = rand(-5, 10) / 1000;
                $defectRate     = max(0, $config['defect_rate'] + $defectVariance);
                $defects        = (int) round($produced * $defectRate);

                $records[] = [
                    'plant'             => $plant,
                    'product_line'      => $lineName,
                    'produced_quantity' => $produced,
                    'defect_quantity'   => $defects,
                    'production_date'   => $date->toDateString(),
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('productivities')->insert($chunk);
        }

        $this->command->info('ProductivitySeeder: ' . count($records) . ' records inserted for January 2026.');
    }
}
