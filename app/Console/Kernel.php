<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        \App\Console\Commands\unansweredTicketMail::class,
        \App\Console\Commands\unreadTicketMail::class,
        \App\Console\Commands\SendGoogleChatWebhook::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // $schedule->call(function () {
        //     Mail::to('randres@ics.com.ph')
        //         ->queue(new \App\Mail\UnreadRequestNotif("Subject here", "Content here"));
        // })->everyMinute();

        //$schedule->command('mail:unread')->everyMinute();

        // $schedule->command('mail:unanswered')->everyMinute();

        // $schedule->command('mail:unanswered')->weekdays()->hourly()->between('08:00', '18:00');
        // $schedule->command('mail:unread')->weekdays()->hourly()->between('08:00', '18:00');

        // $schedule->command('webhook:send-chat')->weekdays()->everyMinute()->between('08:00', '18:00');
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
