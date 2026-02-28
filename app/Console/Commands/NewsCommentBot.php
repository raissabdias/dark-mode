<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Services\AiService;
use Illuminate\Console\Command;

class NewsCommentBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:comment-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bot that reads recent news and generates random comments via AI';

    /**
     * Create a new command instance.
     */
    public function __construct(
        protected AiService $aiService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botEmail = 'bot.reader@yourportal.com';

        $newsItems = News::where('is_active', true)
            ->where('published_at', '>=', now()->subDays(60))
            ->latest('published_at')
            ->take(15)
            ->get();

        if ($newsItems->isEmpty()) {
            $this->warn("Nenhuma notícia encontrada nos últimos 7 dias.");
            return;
        }

        foreach ($newsItems as $news) {
            # Probability: 3 in 5 (60%)
            if (rand(1, 100) > 80) {
                $this->line("Skipping: {$news->title} (Luck of the draw)");
                continue;
            }

            $this->info("Reading and thinking about: {$news->title}");

            # Randomly select a tone for the comment to add variety
            $tones = ['informal', 'questionador', 'entusiasta', 'curto e grosso'];
            $selectedTone = $tones[array_rand($tones)];

            # Randomly select a length for the comment
            $lengths = ['muito curto', 'curto', 'curto', 'médio', 'médio', 'longo'];
            $selectedLength = $lengths[array_rand($lengths)];

            $data = $this->aiService->generateShortComment($news->title, $news->content, $selectedTone, $selectedLength);

            if ($data && isset($data['author_name']) && isset($data['comment_body'])) {
                $news->comments()->create([
                    'name' => $data['author_name'],
                    'content' => $data['comment_body'],
                    'email' => $botEmail,
                    'is_approved' => true,
                    'is_read' => false
                ]);

                $this->info("Posted as: " . $data['author_name'] . " | Tone: {$selectedTone} | Length: {$selectedLength}" . " | Comment: " . $data['comment_body'] . "\n");
            }
            
            sleep(22);
        }

        $this->info('Bot finished its round!');
    }
}
