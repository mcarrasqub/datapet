<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendVaccinationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pet:send-vaccine-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect upcoming vaccinations (next 3 days) and generate WhatsApp reminders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Escaneando vacunas próximas a vencer...');

        $processed = resolve(\App\Services\VaccinationReminderService::class)->sendUpcomingReminders();

        $this->info("Escaneo completado. Se procesaron {$processed} alertas.");

        return 0;
    }
}
