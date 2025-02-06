<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('zoho:refresh-token')
            ->everyMinute()
            ->when(function () {
                return now()->minute % 50 == 0; // Запуск каждые 50 минут
            });

        // Это временный лог для проверки работы планировщика
        $schedule->call(function () {
            Log::info('Токен обновлен!');
        })->everyMinute(); // Тоже раз в минуту
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
