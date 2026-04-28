<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


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
}
