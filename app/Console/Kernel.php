<?php

namespace App\Console;

use App\Console\Commands\AddFriend;
use App\Console\Commands\CommentBlog;
use App\Console\Commands\CrawlName;
use App\Console\Commands\JoinGroup;
use App\Console\Commands\LikeBlog;
use App\Console\Commands\UploadUser;
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
        CrawlName::class,
        UploadUser::class,
        LikeBlog::class,
        JoinGroup::class,
        CommentBlog::class,
        AddFriend::class,
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
