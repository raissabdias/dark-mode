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

    public function generateShortComment(string $title, string $content, string $tone = 'random', string $length = 'médio'): ?string
    {
        try {
            # Clean the content to avoid sending too much data to the API and to prevent HTML tags from influencing the comment
            $cleanContent = mb_strimwidth(strip_tags($content), 0, 1000, "...");

            $lengthInstruction = match ($length) {
                'muito curto' => 'no máximo 3 ou 4 palavras.',
                'curto' => 'uma frase bem curta e direta.',
                'longo' => 'duas frases, sendo mais detalhado e opinativo.',
                default => 'uma frase de tamanho normal.'
            };

            $response = Http::withToken($this->apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "Você é um leitor brasileiro comum. 
                                Tom: {$tone}. 
                                Tamanho: O comentário deve ter {$lengthInstruction}
                                REGRAS:
                                1. NUNCA use clichês como 'Que legal' ou 'Incrível'.
                                2. Se for longo, não seja formal demais. 
                                3. Cometa erros de digitação sutis (ex: 'vcs', 'ta', 'mt'), mas não se apegue nisso, minoria dos comentários.
                                4. Evite usar palavras difíceis ou jargões.
                                5. Seja autêntico, como um comentário genuíno de um leitor real.
                                7. Use expressões idiomáticas brasileiras, variando entre o fim da frase e o início da próxima.
                                8. Não se repita muito com falando que está curioso, ou ansioso.
                                9. Em alguns casos, finja ser fã da banda ou do artista citado.
                                10. Traga uma perspectiva pessoal, como se estivesse relacionando a notícia com algo da vida real ou com experiências pessoais. 
                                11. Trago em alguns casos, alguma menção ou sugestão de outras bandas do mesmo estilo.
                                12. Troque ocasionalmente uma letra por outra vizinha (ex: 'legal' por 'leagl' ou 'bom' por 'vbom') para parecer que foi digitado rápido no celular.
                                13. Nunca traga o nome das bandas em maiúsculo, e nem o nome das músicas, ou filmes, entre aspas.
                            "
                        ],
                        [
                            'role' => 'user',
                            'content' => "Notícia: {$title}\nResumo: {$cleanContent}"
                        ],
                    ],
                    'temperature' => 1,
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
