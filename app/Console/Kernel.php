<?php

namespace App\Console;

use App\Console\Commands\CrawlFile;
use App\Console\Commands\CrawlNews;
use App\Console\Commands\CrawlSitemap;
use App\Console\Commands\RunCrawler;
use App\Console\Commands\UploadDocument;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RunCrawler::class,
        UploadDocument::class,
        CrawlSitemap::class,
        CrawlNews::class,
        CrawlFile::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
