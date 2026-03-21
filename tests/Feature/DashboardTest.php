<?php

namespace Tests\Feature;

use App\Productivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_returns_200(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_displays_all_product_lines(): void
    {
        foreach (Productivity::PRODUCT_LINES as $line) {
            Productivity::create([
                'plant'             => 'Planta A',
                'product_line'      => $line,
                'produced_quantity' => 100,
                'defect_quantity'   => 5,
                'production_date'   => '2026-01-15',
            ]);
        }

        $response = $this->get('/');

        $response->assertStatus(200);
        foreach (Productivity::PRODUCT_LINES as $line) {
            $response->assertSee($line);
        }
    }

    /** @test */
    public function dashboard_filters_by_product_line(): void
    {
        Productivity::create([
            'plant'             => 'Planta A',
            'product_line'      => Productivity::LINE_TV,
            'produced_quantity' => 500,
            'defect_quantity'   => 10,
            'production_date'   => '2026-01-10',
        ]);

        Productivity::create([
            'plant'             => 'Planta A',
            'product_line'      => Productivity::LINE_GELADEIRA,
            'produced_quantity' => 300,
            'defect_quantity'   => 8,
            'production_date'   => '2026-01-10',
        ]);

        $response = $this->get('/?product_line=' . urlencode(Productivity::LINE_TV));

        $response->assertStatus(200);

        // The summary should show only TV with 1 card
        $response->assertSee('1 registro(s)');

        // The select should have TV as selected
        $response->assertSee('selected');
    }

    /** @test */
    public function dashboard_ignores_invalid_product_line_filter(): void
    {
        Productivity::create([
            'plant'             => 'Planta A',
            'product_line'      => Productivity::LINE_TV,
            'produced_quantity' => 500,
            'defect_quantity'   => 10,
            'production_date'   => '2026-01-10',
        ]);

        $response = $this->get('/?product_line=Fogão');

        // Must not break — shows all lines (unfiltered)
        $response->assertStatus(200);
        $response->assertSee(Productivity::LINE_TV);
    }

    /** @test */
    public function dashboard_shows_empty_state_with_no_data(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Nenhum dado encontrado');
    }
}




