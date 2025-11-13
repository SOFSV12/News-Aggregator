<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
use Illuminate\Console\Command;

class FetchArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from all defined sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching job to fetch articles...');

        // Option 1: Queue it
        FetchArticlesJob::dispatch()->onQueue('default');

        // Option 2: Run synchronously (no queue)
        // $articleService->fetchFromAllSources();

        $this->info('Fetch job dispatched successfully!');
    }
}
