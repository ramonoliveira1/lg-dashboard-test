<?php

namespace Tests\Unit;

use App\Services\GeminiService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    private function makeSummary(): Collection
    {
        return new Collection([
            (object) [
                'product_line'   => 'TV',
                'total_produced' => 500,
                'total_defects'  => 10,
                'efficiency'     => 98.0,
            ],
            (object) [
                'product_line'   => 'Geladeira',
                'total_produced' => 300,
                'total_defects'  => 8,
                'efficiency'     => 97.33,
            ],
        ]);
    }

    private function makeService(array $responses): GeminiService
    {
        $mock    = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        return new GeminiService($client);
    }

    /** @test */
    public function it_returns_analysis_text_on_success(): void
    {
        config(['services.gemini.key' => 'AIzaTestKey0123456789']);

        $body = [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'A planta apresentou boa eficiência.'],
                        ],
                    ],
                ],
            ],
        ];

        $service = $this->makeService([
            new Response(200, [], json_encode($body)),
        ]);

        $result = $service->analyze($this->makeSummary());

        $this->assertEquals('A planta apresentou boa eficiência.', $result);
    }

    /** @test */
    public function it_throws_on_invalid_token(): void
    {
        config(['services.gemini.key' => 'AIzaInvalidKey0000000']);

        $service = $this->makeService([
            new Response(403, [], json_encode(['error' => 'forbidden'])),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Token inválido');

        $service->analyze($this->makeSummary());
    }

    /** @test */
    public function it_returns_fallback_when_response_has_no_text(): void
    {
        config(['services.gemini.key' => 'AIzaTestKey0123456789']);

        $body = ['candidates' => [['content' => ['parts' => []]]]];

        $service = $this->makeService([
            new Response(200, [], json_encode($body)),
        ]);

        $result = $service->analyze($this->makeSummary());

        $this->assertEquals('Não foi possível gerar a análise.', $result);
    }

    /** @test */
    public function it_throws_on_rate_limit(): void
    {
        config(['services.gemini.key' => 'AIzaTestKey0123456789']);

        $service = $this->makeService([
            new Response(429, [], json_encode(['error' => 'quota exceeded'])),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Limite de requisições atingido');

        $service->analyze($this->makeSummary());
    }

    /** @test */
    public function it_throws_when_key_not_configured(): void
    {
        config(['services.gemini.key' => '']);

        $service = $this->makeService([]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API Key do Gemini não configurada');

        $service->analyze($this->makeSummary());
    }

    /** @test */
    public function is_configured_returns_false_when_no_key(): void
    {
        config(['services.gemini.key' => '']);

        $service = new GeminiService();

        $this->assertFalse($service->isConfigured());
    }

    /** @test */
    public function is_configured_returns_true_when_key_set(): void
    {
        config(['services.gemini.key' => 'a-valid-api-key-1234567890']);

        $service = new GeminiService();

        $this->assertTrue($service->isConfigured());
    }
}
