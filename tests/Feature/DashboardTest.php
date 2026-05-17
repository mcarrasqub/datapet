<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\MedicalExam;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard_with_all_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Dummy data for metrics
        $doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);

        MedicalRecord::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'visited_at' => now(),
            'reason' => 'Test reason',
            'diagnosis' => 'Test diagnosis',
            'notes' => 'Test notes',
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $pet->id,
            'date' => now()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'scheduled',
            'reason' => 'Consulta',
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin');
        $response->assertViewHasAll([
            'totalUsers',
            'totalAdmins',
            'totalDoctors',
            'totalClients',
            'consultasSemana',
            'consultasHoy',
            'growthPercentage',
            'recentActivities',
            'agendaHoy'
        ]);
    }

    public function test_doctor_can_view_dashboard_with_doctor_metrics(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($doctor);

        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);

        MedicalExam::create([
            'pet_id' => $pet->id,
            'uploaded_by' => $client->id,
            'title' => 'Examen pendiente',
            'file_path' => 'dummy',
            'original_name' => 'dummy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 100,
            'uploaded_at' => now(),
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.doctor');
        $response->assertViewHasAll([
            'totalPatients',
            'consultasHoy',
            'consultasMes',
            'examsPendientes',
            'pendingExams',
            'agendaHoy'
        ]);
    }

    public function test_client_is_redirected_from_dashboard(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $this->actingAs($client);

        $response = $this->get(route('dashboard.index'));

        $response->assertRedirect(route('home.index'));
    }
}
