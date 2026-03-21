<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveApiKeyRequest;
use App\Services\GeminiServiceInterface;
use App\Services\ProductivityServiceInterface;
use Illuminate\Http\JsonResponse;

class AnalysisController extends Controller
{
    /** @var GeminiServiceInterface */
    private GeminiServiceInterface $geminiService;

    /** @var ProductivityServiceInterface */
    private ProductivityServiceInterface $productivityService;

    public function __construct(
        GeminiServiceInterface $geminiService,
        ProductivityServiceInterface $productivityService
    ) {
        $this->geminiService       = $geminiService;
        $this->productivityService = $productivityService;
    }

    /**
     * Check if the Gemini API key is configured.
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'has_key' => $this->geminiService->isConfigured(),
        ]);
    }

    /**
     * Save the Gemini API key to the .env file.
     */
    public function configure(SaveApiKeyRequest $request): JsonResponse
    {
        $this->geminiService->saveApiKey($request->input('api_key'));

        return response()->json(['message' => 'API Key salva com sucesso.']);
    }

    /**
     * Generate AI analysis of production data.
     */
    public function generate(): JsonResponse
    {
        if (!$this->geminiService->isConfigured()) {
            return response()->json([
                'error' => 'API Key do Gemini não configurada.',
            ], 422);
        }

        $summary = $this->productivityService->getSummary();

        if ($summary->isEmpty()) {
            return response()->json([
                'error' => 'Não há dados de produção para analisar.',
            ], 404);
        }

        try {
            $analysis = $this->geminiService->analyze($summary);

            return response()->json(['analysis' => $analysis]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}

