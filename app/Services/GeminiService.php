<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use RuntimeException;

class GeminiService implements GeminiServiceInterface
{
    /** @var Client */
    private Client $http;

    public function __construct(Client $http = null)
    {
        $this->http = $http ?? new Client(['timeout' => 30]);
    }

    public function isConfigured(): bool
    {
        return strlen(config('services.gemini.key', '')) >= 10;
    }

    public function saveApiKey(string $apiKey): void
    {
        $envPath = app()->environmentFilePath();
        $content = file_get_contents($envPath);

        $key = 'GEMINI_API_KEY';

        if (preg_match("/^$key=.*/m", $content)) {
            $content = preg_replace("/^$key=.*/m", "$key=$apiKey", $content);
        } else {
            $content .= "\n$key=$apiKey\n";
        }

        file_put_contents($envPath, $content);

        config(['services.gemini.key' => $apiKey]);
    }

    public function analyze(Collection $summary): string
    {
        $apiKey = config('services.gemini.key', '');

        if (strlen($apiKey) < 10) {
            throw new RuntimeException('API Key do Gemini não configurada.');
        }

        $prompt = $this->buildPrompt($summary);
        $model  = config('services.gemini.model', 'gemini-flash-latest');

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $apiKey
        );

        try {
            $response = $this->http->post($url, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 1024,
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return $body['candidates'][0]['content']['parts'][0]['text']
                ?? 'Não foi possível gerar a análise.';
        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;

            if ($status === 429) {
                throw new RuntimeException('Limite de requisições atingido. Aguarde alguns minutos e tente novamente.');
            }

            if ($status === 400 || $status === 403) {
                throw new RuntimeException('Token inválido ou sem permissão. Verifique sua API Key do Gemini.');
            }

            throw new RuntimeException('Erro ao conectar com a API do Gemini: ' . $e->getMessage());
        }
    }

    /**
     * Build the prompt with production context.
     *
     * @param  Collection  $summary
     * @return string
     */
    private function buildPrompt(Collection $summary): string
    {
        $lines = $summary->map(function ($row) {
            return sprintf(
                '- %s: %d produzidos, %d defeitos, eficiência %.1f%%',
                $row->product_line,
                $row->total_produced,
                $row->total_defects,
                $row->efficiency
            );
        })->implode("\n");

        return <<<PROMPT
Você é um analista de produção industrial da LG Electronics.
Analise os dados de produção da Planta A referentes a janeiro de 2026.

Dados consolidados por linha de produto:
{$lines}

Fórmula de eficiência: ((Produzido - Defeitos) / Produzido) × 100

Por favor, forneça em português:
1. Um resumo geral da performance da planta.
2. Destaque as linhas com melhor e pior desempenho.
3. Possíveis causas para linhas com eficiência mais baixa.
4. Recomendações práticas de melhoria.

Seja objetivo e use no máximo 300 palavras.
PROMPT;
    }
}

