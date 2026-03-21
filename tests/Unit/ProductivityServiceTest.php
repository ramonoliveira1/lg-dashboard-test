<?php

namespace Tests\Unit;

use App\Productivity;
use App\Repositories\ProductivityRepositoryInterface;
use App\Services\ProductivityService;
use App\Services\ProductivityServiceInterface;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class ProductivityServiceTest extends TestCase
{
    /** @var ProductivityServiceInterface */
    private $service;

    /** @var ProductivityRepositoryInterface|\Mockery\MockInterface */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ProductivityRepositoryInterface::class);
        $this->service    = new ProductivityService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_calculates_efficiency_correctly(): void
    {
        $this->assertEquals(95.0, $this->service->calculateEfficiency(300, 15));
    }

    /** @test */
    public function it_returns_zero_efficiency_when_nothing_produced(): void
    {
        $this->assertEquals(0.0, $this->service->calculateEfficiency(0, 0));
    }

    /** @test */
    public function it_returns_100_percent_when_no_defects(): void
    {
        $this->assertEquals(100.0, $this->service->calculateEfficiency(500, 0));
    }

    /** @test */
    public function it_rounds_efficiency_to_two_decimal_places(): void
    {
        $this->assertEquals(97.67, $this->service->calculateEfficiency(300, 7));
    }

    /** @test */
    public function it_handles_all_defects(): void
    {
        $this->assertEquals(0.0, $this->service->calculateEfficiency(200, 200));
    }

    /** @test */
    public function it_calculates_efficiency_with_high_volume(): void
    {
        $this->assertEquals(96.5, $this->service->calculateEfficiency(10000, 350));
    }

    /** @test */
    public function it_handles_single_unit_produced_no_defects(): void
    {
        $this->assertEquals(100.0, $this->service->calculateEfficiency(1, 0));
    }

    /** @test */
    public function it_handles_single_unit_produced_one_defect(): void
    {
        $this->assertEquals(0.0, $this->service->calculateEfficiency(1, 1));
    }

    /** @test */
    public function it_returns_product_lines_from_model_constants(): void
    {
        $lines = $this->service->getProductLines();

        $this->assertSame(Productivity::PRODUCT_LINES, $lines);
    }

    /** @test */
    public function it_has_exactly_four_product_lines(): void
    {
        $this->assertCount(4, $this->service->getProductLines());
    }

    /** @test */
    public function product_lines_contain_all_expected_values(): void
    {
        $lines = $this->service->getProductLines();

        $this->assertContains(Productivity::LINE_GELADEIRA, $lines);
        $this->assertContains(Productivity::LINE_MAQUINA_DE_LAVAR, $lines);
        $this->assertContains(Productivity::LINE_TV, $lines);
        $this->assertContains(Productivity::LINE_AR_CONDICIONADO, $lines);
    }

    /** @test */
    public function model_validates_known_product_line(): void
    {
        $this->assertTrue(Productivity::isValidProductLine(Productivity::LINE_TV));
    }

    /** @test */
    public function model_rejects_unknown_product_line(): void
    {
        $this->assertFalse(Productivity::isValidProductLine('Fogão'));
    }

    /** @test */
    public function model_rejects_empty_string_as_product_line(): void
    {
        $this->assertFalse(Productivity::isValidProductLine(''));
    }

    /** @test */
    public function model_efficiency_accessor_calculates_correctly(): void
    {
        $model = new Productivity([
            'produced_quantity' => 400,
            'defect_quantity'   => 20,
        ]);

        $this->assertEquals(95.0, $model->efficiency);
    }

    /** @test */
    public function model_efficiency_accessor_returns_zero_when_nothing_produced(): void
    {
        $model = new Productivity([
            'produced_quantity' => 0,
            'defect_quantity'   => 0,
        ]);

        $this->assertEquals(0.0, $model->efficiency);
    }

    /** @test */
    public function get_summary_delegates_to_repository(): void
    {
        $expected = new Collection(['summary_data']);

        $this->repository
            ->shouldReceive('getSummary')
            ->once()
            ->with('Planta A', 2026, 1, null)
            ->andReturn($expected);

        $result = $this->service->getSummary();

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function get_summary_passes_product_line_filter_to_repository(): void
    {
        $expected = new Collection(['filtered_data']);

        $this->repository
            ->shouldReceive('getSummary')
            ->once()
            ->with('Planta A', 2026, 1, Productivity::LINE_TV)
            ->andReturn($expected);

        $result = $this->service->getSummary(Productivity::LINE_TV);

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function get_daily_records_delegates_to_repository(): void
    {
        $expected = new Collection(['daily_data']);

        $this->repository
            ->shouldReceive('getDailyRecords')
            ->once()
            ->with('Planta A', 2026, 1, null)
            ->andReturn($expected);

        $result = $this->service->getDailyRecords();

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function get_daily_records_passes_product_line_filter_to_repository(): void
    {
        $expected = new Collection(['filtered_daily']);

        $this->repository
            ->shouldReceive('getDailyRecords')
            ->once()
            ->with('Planta A', 2026, 1, Productivity::LINE_GELADEIRA)
            ->andReturn($expected);

        $result = $this->service->getDailyRecords(Productivity::LINE_GELADEIRA);

        $this->assertSame($expected, $result);
    }
}
