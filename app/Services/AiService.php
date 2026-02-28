<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * @param string $apiKey Chave da API vinda do Provider
     */
    public function __construct(
        protected string $apiKey
    ) {}

    public function generateShortComment(string $title, string $content): ?string
    {
        try {
            # Clean the content to avoid sending too much data to the API and to prevent HTML tags from influencing the comment
            $cleanContent = mb_strimwidth(strip_tags($content), 0, 1000, "...");

            $response = Http::withToken($this->apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Você é um leitor comum de um portal de notícias brasileiro. 
                                          Escreva um comentário curto (máximo 1 frase) sobre a notícia. 
                                          Seja natural, use tom informal e não use hashtags.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Título: {$title}\nConteúdo: {$cleanContent}"
                        ],
                    ],
                    'temperature' => 0.8,
                ]);

            /*
            if ($response->failed()) {
                // Isso vai imprimir o erro real da OpenAI no seu console do Sail
                dump("Erro na API: " . $response->status());
                dump($response->json()); 
                return null;
            }
            */

            return $response->json()['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            Log::error("AiService Error: " . $e->getMessage());
            return null;
        }
    }
}
