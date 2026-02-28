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
        $newsItems = News::where('is_active', true)
            ->latest('published_at')
            ->take(10)
            ->get();

        $names = ['Gabriel Souza', 'Ana Beatriz', 'Lucas Oliveira', 'Mariana Costa', 'Felipe Santos', 'Juliana Lima'];

        foreach ($newsItems as $news) {
            # Probability: 3 in 5 (60%)
            if (rand(1, 100) > 60) {
                $this->line("Skipping: {$news->title} (Luck of the draw)");
                continue;
            }

            # Avoid double commenting on the same news
            $botEmail = 'bot.reader@yourportal.com';
            if ($news->comments()->where('email', $botEmail)->exists()) {
                $this->line("Already commented on: {$news->title}");
                continue;
            }

            $this->info("Reading and thinking about: {$news->title}");

            $commentContent = $this->aiService->generateShortComment($news->title, $news->content);

            if ($commentContent) {
                $this->info("AI generated: " . $commentContent);
                $news->comments()->create([
                    'name' => $names[array_rand($names)],
                    'email' => $botEmail,
                    'content' => $commentContent,
                    'is_read' => false
                ]);

                $this->info("Comment posted successfully!");
            }
            
            sleep(22);
        }

        $this->info('Bot finished its round!');
    }
}
