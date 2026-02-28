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

    public function generateShortComment(string $title, string $content, string $tone = 'random', string $length = 'médio'): ?array
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
                                Responda estritamente em formato JSON com as chaves 'author_name' e 'comment_body'.
                                Tom: {$tone}. 
                                Tamanho: O comentário deve ter {$lengthInstruction}
                                REGRAS PARA O CONTEÚDO:
                                1. NUNCA use clichês como 'Que legal' ou 'Incrível'.
                                2. Se for longo, não seja formal demais. 
                                3. Cometa erros de digitação sutis (ex: 'vcs', 'ta', 'mt'), mas não se apegue nisso, minoria dos comentários.
                                4. Evite usar palavras difíceis ou jargões.
                                5. Seja autêntico, como um comentário genuíno de um leitor real.
                                7. Use expressões idiomáticas brasileiras, variando entre partes diferentes da frase (ex: início, fim, etc).
                                8. Não se repita muito com falando que está curioso, ou ansioso.
                                9. Em alguns casos, finja ser fã da banda ou do artista citado.
                                10. Traga uma perspectiva pessoal, como se estivesse relacionando a notícia com algo da vida real ou com experiências pessoais. 
                                11. Trago em alguns casos, alguma menção ou sugestão de outras bandas do mesmo estilo.
                                12. Troque ocasionalmente uma letra por outra vizinha (ex: 'legal' por 'leagl' ou 'bom' por 'vbom') para parecer que foi digitado rápido no celular.
                                13. Nunca escreva nome das bandas em maiúsculo (ex: THE BEATLES, IRON MAIDEN), e nem o nome das músicas, ou filmes, entre aspas.
                                14. PROIBIDO começar frases com 'Poxa', 'Pô', 'Mano', 'Vixe' ou 'Nossa' em mais de 10% dos casos.
                                15. Comente uns kkkk ou rs de vez em quando, mas não exagere.
                                16. Erre o use da vírgula de vez em quando, mas sem exagerar.

                                REGRAS PARA O NOME (author_name):
                                1. Crie nomes realistas e variados: as vezes nome completo, as vezes só primeiro nome, as vezes nicknames (ex: metal_vibe, Ana Paula, RODRIGO).
                                2. O nome deve combinar com a personalidade do comentário.
                                3. Varie radicalmente: 'Carlos_82', 'ana clara', 'MARCOS', 'MetalHead_BR', 'Felipe S.'.
                            "
                        ],
                        [
                            'role' => 'user',
                            'content' => "Notícia: {$title}\nResumo: {$cleanContent}"
                        ],
                    ],
                    'temperature' => 1,
                    'response_format' => ['type' => 'json_object'],
                ]);

            /*
            if ($response->failed()) {
                // Isso vai imprimir o erro real da OpenAI no seu console do Sail
                dump("Erro na API: " . $response->status());
                dump($response->json()); 
                return null;
            }
            */

            if ($response->successful()) {
                $jsonString = $response->json('choices.0.message.content');
                return json_decode($jsonString, true);
            }

            return null;
        } catch (\Exception $e) {
            Log::error("AiService Error: " . $e->getMessage());
            return null;
        }
    }
}
