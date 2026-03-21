<?php

namespace Tests\Feature;

use App\Services\GeminiServiceInterface;
use App\Productivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function status_returns_false_when_no_key(): void
    {
        config(['services.gemini.key' => '']);

        $response = $this->getJson('/analysis/status');

        $response->assertOk()
                 ->assertJson(['has_key' => false]);
    }

    /** @test */
    public function status_returns_true_when_key_configured(): void
    {
        config(['services.gemini.key' => 'a-valid-api-key-1234567890']);

        $response = $this->getJson('/analysis/status');

        $response->assertOk()
                 ->assertJson(['has_key' => true]);
    }

    /** @test */
    public function configure_returns_422_without_api_key(): void
    {
        $response = $this->postJson('/analysis/configure', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('api_key');
    }

    /** @test */
    public function configure_returns_422_with_short_api_key(): void
    {
        $response = $this->postJson('/analysis/configure', ['api_key' => 'abc']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('api_key');
    }

    /** @test */
    public function configure_saves_key_successfully(): void
    {
        $mock = \Mockery::mock(GeminiServiceInterface::class);
        $mock->shouldReceive('saveApiKey')
             ->once()
             ->with('a-valid-api-key-1234567890');

        $this->app->instance(GeminiServiceInterface::class, $mock);

        $response = $this->postJson('/analysis/configure', [
            'api_key' => 'a-valid-api-key-1234567890',
        ]);

        $response->assertOk()
                 ->assertJson(['message' => 'API Key salva com sucesso.']);
    }

    /** @test */
    public function generate_returns_422_when_key_not_configured(): void
    {
        $mock = \Mockery::mock(GeminiServiceInterface::class);
        $mock->shouldReceive('isConfigured')->andReturn(false);

        $this->app->instance(GeminiServiceInterface::class, $mock);

        $response = $this->postJson('/analysis/generate');

        $response->assertStatus(422)
                 ->assertJson(['error' => 'API Key do Gemini não configurada.']);
    }

    /** @test */
    public function generate_returns_404_when_no_production_data(): void
    {
        $mock = \Mockery::mock(GeminiServiceInterface::class);
        $mock->shouldReceive('isConfigured')->andReturn(true);

        $this->app->instance(GeminiServiceInterface::class, $mock);

        $response = $this->postJson('/analysis/generate');

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Não há dados de produção para analisar.']);
    }

    /** @test */
    public function generate_returns_analysis_on_success(): void
    {
        Productivity::create([
            'plant'             => 'Planta A',
            'product_line'      => Productivity::LINE_TV,
            'produced_quantity' => 500,
            'defect_quantity'   => 10,
            'production_date'   => '2026-01-10',
        ]);

        $mock = \Mockery::mock(GeminiServiceInterface::class);
        $mock->shouldReceive('isConfigured')->andReturn(true);
        $mock->shouldReceive('analyze')
             ->once()
             ->andReturn('Análise gerada com sucesso.');

        $this->app->instance(GeminiServiceInterface::class, $mock);

        $response = $this->postJson('/analysis/generate');

        $response->assertOk()
                 ->assertJson(['analysis' => 'Análise gerada com sucesso.']);
    }

    /** @test */
    public function generate_returns_422_when_gemini_throws(): void
    {
        Productivity::create([
            'plant'             => 'Planta A',
            'product_line'      => Productivity::LINE_TV,
            'produced_quantity' => 500,
            'defect_quantity'   => 10,
            'production_date'   => '2026-01-10',
        ]);

        $mock = \Mockery::mock(GeminiServiceInterface::class);
        $mock->shouldReceive('isConfigured')->andReturn(true);
        $mock->shouldReceive('analyze')
             ->once()
             ->andThrow(new \RuntimeException('Token inválido ou sem permissão.'));

        $this->app->instance(GeminiServiceInterface::class, $mock);

        $response = $this->postJson('/analysis/generate');

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Token inválido ou sem permissão.']);
    }
}

