<?php

namespace App\Services;

use Illuminate\Support\Collection;

interface GeminiServiceInterface
{
    /**
     * Sends production data to Gemini and returns the AI analysis text.
     *
     * @param  Collection  $summary
     * @return string
     */
    public function analyze(Collection $summary): string;

    /**
     * Check if the Gemini API key is configured.
     *
     * @return bool
     */
    public function isConfigured(): bool;

    /**
     * Save the API key to the .env file.
     *
     * @param  string  $apiKey
     * @return void
     */
    public function saveApiKey(string $apiKey): void;
}
