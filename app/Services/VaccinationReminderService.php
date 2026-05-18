<?php

namespace App\Services;

use App\Models\Vaccination;
use App\Models\VaccinationReminder;

class VaccinationReminderService
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Scan upcoming vaccinations (next 3 days) and send/log reminders.
     *
     * @return int Number of reminders processed.
     */
    public function sendUpcomingReminders(): int
    {
        $startDate = now()->toDateString();
        $endDate = now()->addDays(3)->toDateString();

        $upcomingVaccinations = Vaccination::with(['pet.owner'])
            ->whereBetween('next_due_date', [$startDate, $endDate])
            ->get();

        $processedCount = 0;

        foreach ($upcomingVaccinations as $vaccination) {
            $pet = $vaccination->pet;
            if (! $pet) {
                continue;
            }

            $owner = $pet->owner;
            if (! $owner) {
                continue;
            }

            // Exclude already sent active, completed or dismissed reminders
            $alreadyReminded = VaccinationReminder::where('vaccination_id', $vaccination->id)
                ->whereIn('status', ['sent', 'completed', 'dismissed'])
                ->exists();

            if ($alreadyReminded) {
                continue;
            }

            $ownerName = trim($owner->name.' '.($owner->lastname ?? ''));
            $petName = $pet->getName();
            $vaccineType = $vaccination->vaccine_type;
            $dueDate = $vaccination->next_due_date->format('Y-m-d');
            $phone = $owner->phone ?? '3000000000';

            $message = sprintf(
                '¡Hola %s! Te recordamos de A.N Hospital Veterninario que tu mascota *%s* tiene programada su vacuna *%s* para el *%s*. Por favor agenda su cita a tiempo.',
                $ownerName,
                $petName,
                $vaccineType,
                $dueDate
            );

            // Send via WhatsApp Service
            $success = $this->whatsAppService->sendMessage($phone, $message);

            // Save to database
            VaccinationReminder::create([
                'vaccination_id' => $vaccination->id,
                'pet_id' => $pet->id,
                'user_id' => $owner->id,
                'phone' => $phone,
                'message' => $message,
                'status' => $success ? 'sent' : 'failed',
                'sent_at' => now(),
            ]);

            $processedCount++;
        }

        return $processedCount;
    }
}
