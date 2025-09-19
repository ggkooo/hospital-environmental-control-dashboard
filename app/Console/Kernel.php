<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Defina os comandos Artisan customizados.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\GenerateApiKey::class,
    ];

    /**
     * Defina a programação de tarefas do aplicativo.
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Registre os comandos para a aplicação.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

