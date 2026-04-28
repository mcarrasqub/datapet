<?php

namespace Tests\Feature;

use App\Models\DoctorTask;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_update_own_status(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $task = DoctorTask::create([
            'doctor_id' => $doctor->id,
            'title' => 'Revisar paciente',
            'status' => 'pending',
            'priority' => 'medium',
            'is_system' => false,
            'task_key' => 'manual:' . rand(1,999),
        ]);

        $this->actingAs($doctor)
             ->patch(route('tasks.updateOwnStatus', $task), ['status' => 'completed'])
             ->assertRedirect();

        $this->assertDatabaseHas('doctor_tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    public function test_non_admin_cannot_delete_tasks(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $task = DoctorTask::create([
            'doctor_id' => $doctor->id,
            'title' => 'Tarea inamovible',
            'status' => 'pending',
            'priority' => 'low',
            'is_system' => false,
            'task_key' => 'manual:unique',
        ]);

        $this->actingAs($doctor)
             ->delete(route('tasks.destroy', $task))
             ->assertStatus(403);
    }
}