<?php

namespace Tests\Feature;

use App\Models\DoctorTask;
use App\Models\MedicalExam;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DoctorTaskControllerTest extends TestCase
{
    use WithFaker;

    public function test_admin_index_creates_system_tasks_from_exams_and_records()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctorA = User::factory()->create(['role' => 'doctor']);
        $doctorB = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);

        $pet = Pet::factory()->create(['user_id' => $client->id]);

        // Medical record incomplete for doctorA
        $record = MedicalRecord::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctorA->id,
            'visited_at' => now(),
            'reason' => 'checkup',
            'diagnosis' => '',
            'treatment' => null,
            'notes' => null,
        ]);

        // Medical exam uploaded by client, not reviewed
        $exam = MedicalExam::create([
            'pet_id' => $pet->id,
            'medical_record_id' => null,
            'uploaded_by' => $client->id,
            'title' => 'Examen externo',
            'original_name' => 'exam.pdf',
            'file_path' => 'exams/exam.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 123,
            'uploaded_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('tasks.index'))
            ->assertStatus(200)
            ->assertViewHas('doctors')
            ->assertViewHas('metrics');

        // After index, system tasks should be created for exam and incomplete record
        $this->assertDatabaseHas('doctor_tasks', [
            'is_system' => 1,
            'source_type' => 'medical_exam',
            'source_id' => $exam->id,
        ]);

        $this->assertDatabaseHas('doctor_tasks', [
            'is_system' => 1,
            'source_type' => 'medical_record',
            'source_id' => $record->id,
        ]);
    }

    public function test_admin_can_update_status_and_destroy_task()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $task = DoctorTask::create([
            'doctor_id' => $doctor->id,
            'title' => 'Tarea manual',
            'description' => 'desc',
            'status' => 'pending',
            'priority' => 'low',
            'is_system' => false,
            'task_key' => 'manual:1',
        ]);

        $this->actingAs($admin)
            ->patch(route('tasks.updateStatus', ['task' => $task->id]), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertDatabaseHas('doctor_tasks', ['id' => $task->id, 'status' => 'completed']);

        // Destroy
        $this->actingAs($admin)
            ->delete(route('tasks.destroy', ['task' => $task->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('doctor_tasks', ['id' => $task->id]);
    }

    public function test_doctor_can_update_own_status_but_others_cannot()
    {
        $doctorOwner = User::factory()->create(['role' => 'doctor']);
        $doctorOther = User::factory()->create(['role' => 'doctor']);

        $task = DoctorTask::create([
            'doctor_id' => $doctorOwner->id,
            'title' => 'Tarea doctor',
            'description' => 'desc',
            'status' => 'pending',
            'priority' => 'medium',
            'is_system' => false,
            'task_key' => 'manual:2',
        ]);

        // Owner can update
        $this->actingAs($doctorOwner)
            ->patch(route('tasks.updateOwnStatus', ['task' => $task->id]), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertDatabaseHas('doctor_tasks', ['id' => $task->id, 'status' => 'completed']);

        // Other doctor cannot update
        $task2 = DoctorTask::create([
            'doctor_id' => $doctorOwner->id,
            'title' => 'Tarea doctor 2',
            'description' => 'desc',
            'status' => 'pending',
            'priority' => 'low',
            'is_system' => false,
            'task_key' => 'manual:3',
        ]);

        $this->actingAs($doctorOther)
            ->patch(route('tasks.updateOwnStatus', ['task' => $task2->id]), ['status' => 'completed'])
            ->assertStatus(403);
    }
}
