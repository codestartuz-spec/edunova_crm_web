<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/** OpenAI Chat Completions bilan ishlash. Kalit faqat .env dan olinadi. */
class OpenAiService
{
    public function configured(): bool
    {
        return filled(config('services.openai.key'));
    }

    /**
     * @param  array<int,array<string,mixed>>  $messages
     * @param  array<int,array<string,mixed>>  $tools
     * @return array<string,mixed> javob xabari (content yoki tool_calls)
     */
    public function chat(array $messages, array $tools = [], bool $json = false): array
    {
        if (! $this->configured()) {
            throw ValidationException::withMessages(['message' => "OpenAI kaliti sozlanmagan. .env faylida OPENAI_API_KEY ni kiriting."]);
        }

        $payload = ['model' => config('services.openai.model'), 'messages' => $messages, 'temperature' => 0.3];
        if ($tools) {
            $payload['tools'] = $tools;
        }
        if ($json) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        try {
            $response = Http::withToken(config('services.openai.key'))->timeout(90)->post(rtrim(config('services.openai.base_url'), '/').'/chat/completions', $payload);
        } catch (ConnectionException) {
            throw ValidationException::withMessages(['message' => "OpenAI bilan aloqa o'rnatilmadi. Internetni tekshirib, qayta urinib ko'ring."]);
        }

        if (! $response->successful()) {
            report(new \RuntimeException('OpenAI xatosi: '.$response->status().' '.mb_substr($response->body(), 0, 300)));

            throw ValidationException::withMessages(['message' => $response->status() === 401
                ? "OpenAI kaliti noto'g'ri yoki bekor qilingan."
                : "OpenAI xizmati javob bermadi (kod {$response->status()}). Keyinroq urinib ko'ring."]);
        }

        return $response->json('choices.0.message') ?? throw ValidationException::withMessages(['message' => "OpenAI bo'sh javob qaytardi."]);
    }
}
