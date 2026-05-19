<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'reminders:appointments';
    protected $description = 'Envía recordatorios de WhatsApp para citas próximas (1-2 días antes)';

    public function handle(): void
    {
        $in1Day  = Carbon::today()->addDay();
        $in2Days = Carbon::today()->addDays(2);

        $appointments = Appointment::with(['pet', 'pet.owner'])
            ->where('status', 'scheduled')
            ->whereBetween('date', [$in1Day->toDateString(), $in2Days->toDateString()])
            ->get();

        foreach ($appointments as $appointment) {
            $pet  = $appointment->pet;
            $user = $pet?->owner;

            if (! $pet || ! $user) {
                continue;
            }

            $yaExiste = AppointmentReminder::where('appointment_id', $appointment->getId())
                ->whereIn('status', ['sent', 'pending'])
                ->exists();

            if ($yaExiste) {
                continue;
            }

            $fecha   = Carbon::parse($appointment->getDate())->format('d/m/Y');
            $hora    = Carbon::parse($appointment->getStartTime())->format('H:i');
            $mensaje = "🐾 Recordatorio DataPet: Tu mascota *{$pet->getName()}* tiene una cita veterinaria el *{$fecha}* a las *{$hora}*. ¡No olvides asistir!";
            $phone   = $user->phone ?? null;

            if (! $phone) {
                continue;
            }

            $this->enviarWhatsApp($phone, $mensaje);

            AppointmentReminder::create([
                'appointment_id' => $appointment->getId(),
                'pet_id'         => $pet->getId(),
                'user_id'        => $user->id,
                'phone'          => $phone,
                'message'        => $mensaje,
                'status'         => 'sent',
                'sent_at'        => now(),
            ]);

            $this->info("Recordatorio enviado a {$user->name} para cita de {$pet->getName()}");
        }

        $this->info('Proceso finalizado.');
    }

    private function enviarWhatsApp(string $phone, string $mensaje): void
    {
        // Aquí va el mismo código que usó tu compañera
        // Pregúntale qué hay dentro del método equivalente en VaccinationReminderController
    }
}