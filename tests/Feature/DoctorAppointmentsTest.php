<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;

    private User $otherDoctor;

    private Pet $doctorPet;

    private Pet $otherDoctorPet;

    private string $date;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create([
            'name' => 'Ana',
            'lastname' => 'Gomez',
            'role' => 'doctor',
        ]);

        $this->otherDoctor = User::factory()->create([
            'name' => 'Luis',
            'lastname' => 'Perez',
            'role' => 'doctor',
        ]);

        $clientOne = User::factory()->create(['role' => 'client']);
        $clientTwo = User::factory()->create(['role' => 'client']);

        $this->doctorPet = Pet::factory()->create(['user_id' => $clientOne->id, 'name' => 'Luna']);
        $this->otherDoctorPet = Pet::factory()->create(['user_id' => $clientTwo->id, 'name' => 'Max']);
        $this->date = now()->addDay()->format('Y-m-d');
    }

    /**
     * Happy path: el doctor puede abrir su agenda y el calendario incluye
     * las vistas diaria y semanal configuradas.
     */
    public function test_doctor_can_view_schedule_calendar_with_daily_and_weekly_views(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->get(route('doctor.appointments.index'));

        $response->assertOk();
        $response->assertSee('Agenda del Día', false);
        $response->assertSee('doctor-calendar.js', false);
        $response->assertSee('/doctor/appointments/events', false);

        $calendarScript = file_get_contents(public_path('js/doctor-calendar.js'));
        $this->assertStringContainsString("initialView: 'timeGridDay'", $calendarScript);
        $this->assertStringContainsString("right: 'dayGridMonth,timeGridWeek,timeGridDay'", $calendarScript);
    }

    /**
     * Happy path + negative útil: el doctor solo ve las citas asignadas a él
     * y no las de otro doctor.
     *
     * Criteria covered:
     * - Doctor only sees assigned appointments
     * - Appointment linked to pet and doctor
     */
    public function test_doctor_only_sees_assigned_appointments_in_events(): void
    {
        Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->doctorPet->id,
            'date' => $this->date,
            'start_time' => '09:00',
            'end_time' => '09:30',
            'status' => 'scheduled',
            'reason' => 'Control general',
        ]);

        Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->doctorPet->id,
            'date' => $this->date,
            'start_time' => '10:00',
            'end_time' => '10:30',
            'status' => 'canceled',
            'reason' => 'Cita cancelada',
        ]);

        Appointment::create([
            'doctor_id' => $this->otherDoctor->id,
            'pet_id' => $this->otherDoctorPet->id,
            'date' => $this->date,
            'start_time' => '11:00',
            'end_time' => '11:30',
            'status' => 'scheduled',
            'reason' => 'No debe aparecer',
        ]);

        $this->actingAs($this->doctor);

        $response = $this->get(route('doctor.appointments.events'));

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'title' => 'Cita: Luna',
        ]);
        $response->assertJsonFragment([
            'title' => 'Cita: Luna',
        ]);
        $response->assertJsonMissing([
            'title' => 'Cita: Max',
        ]);
        $response->assertJsonFragment([
            'status' => 'canceled',
            'pet_name' => 'Luna',
        ]);
        $response->assertJsonFragment([
            'status' => 'scheduled',
            'pet_name' => 'Luna',
        ]);
    }

    public function test_daily_agenda_syncs_with_calendar_events_in_real_time(): void
    {
        $this->actingAs($this->doctor);

        // 1. Create a scheduled appointment for TODAY
        $todayStr = now()->format('Y-m-d');
        $appointment = Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->doctorPet->id,
            'date' => $todayStr,
            'start_time' => '14:00',
            'end_time' => '14:30',
            'status' => 'scheduled',
            'reason' => 'Consulta Urgente Luna',
        ]);

        // Access dashboard, must see "Consulta Urgente Luna"
        $dashboardResponse = $this->get(route('dashboard.index'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Consulta Urgente Luna');
        $dashboardResponse->assertSee('Luna');

        // 2. Cancel the appointment (simulate calendar cancel update)
        $appointment->update([
            'status' => 'canceled',
        ]);

        // Access dashboard, canceled appointments must NOT show up in the active daily agenda
        $dashboardResponse2 = $this->get(route('dashboard.index'));
        $dashboardResponse2->assertOk();
        $dashboardResponse2->assertDontSee('Consulta Urgente Luna');

        // 3. Reschedule the appointment to tomorrow and mark as scheduled (simulate calendar reschedule)
        $tomorrowStr = now()->addDay()->format('Y-m-d');
        $appointment->update([
            'date' => $tomorrowStr,
            'status' => 'scheduled',
        ]);

        // Access dashboard, must NOT see the appointment since it's not today
        $dashboardResponse3 = $this->get(route('dashboard.index'));
        $dashboardResponse3->assertOk();
        $dashboardResponse3->assertDontSee('Consulta Urgente Luna');

        // 4. Create another appointment for today
        Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->doctorPet->id,
            'date' => $todayStr,
            'start_time' => '16:00',
            'end_time' => '16:30',
            'status' => 'scheduled',
            'reason' => 'Corte de uñas Luna',
        ]);

        // Access dashboard, must see the new active appointment
        $dashboardResponse4 = $this->get(route('dashboard.index'));
        $dashboardResponse4->assertOk();
        $dashboardResponse4->assertSee('Corte de uñas Luna');
    }
}
