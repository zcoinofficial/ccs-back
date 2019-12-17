<?php

namespace App\Console;

use App\Console\Commands\GenerateAddresses;
use App\Console\Commands\ProcessProposals;
use App\Console\Commands\UpdateSiteProposals;
use App\Console\Commands\walletNotify;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(ProcessProposals::class)
            ->everyMinute();
        $schedule->command(GenerateAddresses::class)
            ->everyMinute();
        $schedule->command(walletNotify::class)
            ->everyMinute();
        $schedule->command(UpdateSiteProposals::class)
            ->everyMinute();
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
