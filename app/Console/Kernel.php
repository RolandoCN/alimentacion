<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        // aprobacion x job
        'App\Console\Commands\AprobarAlmuerzo',
        'App\Console\Commands\AprobarMerienda',
        'App\Console\Commands\AprobarCena',
        'App\Console\Commands\RegistraCabeceraMenu',
    ];


    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        
        //job para aprobar almuerzo en caso de no haberse realizado la aprobacion desde TH
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
