<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $doctor;
    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User', 'status' => true]);
        $this->doctor = User::factory()->create(['role' => 'doctor', 'name' => 'Doctor User', 'status' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'name' => 'Client User', 'status' => true]);
    }

    /**
     * Test index displays list of users and has metrics.
     */
    public function test_admin_can_view_users_list_with_metrics(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
        $response->assertViewHas('roles');
        $response->assertViewHas('counts');

        $response->assertSee('Admin User');
        $response->assertSee('Doctor User');
        $response->assertSee('Client User');
    }

    /**
     * Test index search filtering.
     */
    public function test_index_can_filter_users_by_search(): void
    {
        $this->actingAs($this->admin);

        // Create unique searchable users
        $searchTarget = User::factory()->create(['name' => 'UniqueSearchTarget', 'role' => 'doctor', 'status' => true]);
        $otherUser = User::factory()->create(['name' => 'AnotherDifferentUser', 'role' => 'client', 'status' => true]);

        // Search by name
        $response = $this->get(route('users.index', ['search' => 'UniqueSearchTarget']));
        $response->assertStatus(200);
        $response->assertSee('UniqueSearchTarget');
        $response->assertDontSee('AnotherDifferentUser');

        // Search by email
        $response2 = $this->get(route('users.index', ['search' => $otherUser->email]));
        $response2->assertStatus(200);
        $response2->assertSee('AnotherDifferentUser');
        $response2->assertDontSee('UniqueSearchTarget');
    }

    /**
     * Test index role filtering.
     */
    public function test_index_can_filter_users_by_role(): void
    {
        $this->actingAs($this->admin);

        // Create unique users with distinct roles and names
        $uniqueDoctor = User::factory()->create(['name' => 'UniqueDoc', 'role' => 'doctor', 'status' => true]);
        $uniqueClient = User::factory()->create(['name' => 'UniqueCli', 'role' => 'client', 'status' => true]);

        // Filter by doctor role
        $response = $this->get(route('users.index', ['role' => 'doctor']));
        $response->assertStatus(200);
        $response->assertSee('UniqueDoc');
        $response->assertDontSee('UniqueCli');

        // Filter by client role
        $response2 = $this->get(route('users.index', ['role' => 'client']));
        $response2->assertStatus(200);
        $response2->assertSee('UniqueCli');
        $response2->assertDontSee('UniqueDoc');
    }

    /**
     * Test index ignores invalid role filter.
     */
    public function test_index_ignores_invalid_role_filter(): void
    {
        $this->actingAs($this->admin);

        // Create unique users
        $uniqueDoctor = User::factory()->create(['name' => 'UniqueDoc', 'role' => 'doctor', 'status' => true]);
        $uniqueClient = User::factory()->create(['name' => 'UniqueCli', 'role' => 'client', 'status' => true]);

        $response = $this->get(route('users.index', ['role' => 'invalid-role']));
        $response->assertStatus(200);
        $response->assertSee('UniqueDoc');
        $response->assertSee('UniqueCli');
    }

    /**
     * Test store creates user successfully.
     */
    public function test_admin_can_create_user_successfully(): void
    {
        $this->actingAs($this->admin);

        $userData = [
            'id' => '1234567890',
            'name' => 'Carlos Perez',
            'email' => 'carlos.perez@datapet.com',
            'role' => 'doctor',
            'password' => 'password123',
        ];

        $response = $this->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => 1234567890,
            'name' => 'Carlos',
            'lastname' => 'Perez',
            'email' => 'carlos.perez@datapet.com',
            'role' => 'doctor',
        ]);

        $createdUser = User::find(1234567890);
        $this->assertTrue(Hash::check('password123', $createdUser->password));
        $this->assertTrue((bool) $createdUser->status);
    }

    /**
     * Test store validation failure.
     */
    public function test_store_fails_with_invalid_data(): void
    {
        $this->actingAs($this->admin);

        $invalidData = [
            'id' => 'not-numeric-or-short',
            'name' => '',
            'email' => 'not-an-email',
            'role' => 'hacker',
            'password' => '123',
        ];

        $response = $this->post(route('users.store'), $invalidData);

        $response->assertSessionHasErrors(['id', 'name', 'email', 'role', 'password']);
    }

    /**
     * Test edit form access.
     */
    public function test_admin_can_access_edit_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('users.edit', $this->doctor));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user');
        $response->assertViewHas('roles');
    }

    /**
     * Test edit form access by non-admin is forbidden.
     */
    public function test_non_admin_cannot_access_edit_form(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->get(route('users.edit', $this->client));

        $response->assertStatus(403);
    }

    /**
     * Test update modifies user details.
     */
    public function test_admin_can_update_user_successfully(): void
    {
        $this->actingAs($this->admin);

        $updateData = [
            'name' => 'Doctor Lopez Silva',
            'email' => 'doctor.lopez@datapet.com',
            'role' => 'doctor',
            // No password sent, should preserve old password
        ];

        $oldPassword = $this->doctor->password;

        $response = $this->put(route('users.update', $this->doctor), $updateData);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->doctor->refresh();
        $this->assertEquals('Doctor', $this->doctor->name);
        $this->assertEquals('Lopez Silva', $this->doctor->lastname);
        $this->assertEquals('doctor.lopez@datapet.com', $this->doctor->email);
        $this->assertEquals($oldPassword, $this->doctor->password);
    }

    /**
     * Test update with new password.
     */
    public function test_admin_can_update_user_password(): void
    {
        $this->actingAs($this->admin);

        $updateData = [
            'name' => 'Doctor Lopez',
            'email' => 'doctor.lopez@datapet.com',
            'role' => 'doctor',
            'password' => 'new_secure_password',
        ];

        $response = $this->put(route('users.update', $this->doctor), $updateData);

        $response->assertRedirect(route('users.index'));

        $this->doctor->refresh();
        $this->assertTrue(Hash::check('new_secure_password', $this->doctor->password));
    }

    /**
     * Test update validation handles unique email properly.
     */
    public function test_update_validation_fails_if_email_is_taken_by_another_user(): void
    {
        $this->actingAs($this->admin);

        $updateData = [
            'name' => 'Doctor User Updated',
            'email' => $this->client->email, // Taken by client
            'role' => 'doctor',
        ];

        $response = $this->put(route('users.update', $this->doctor), $updateData);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test update allows same email for the updated user.
     */
    public function test_update_allows_same_email_for_the_same_user(): void
    {
        $this->actingAs($this->admin);

        $updateData = [
            'name' => 'Doctor User Updated',
            'email' => $this->doctor->email, // Same email
            'role' => 'doctor',
        ];

        $response = $this->put(route('users.update', $this->doctor), $updateData);

        $response->assertRedirect(route('users.index'));
        $this->assertEquals($this->doctor->email, $this->doctor->fresh()->email);
    }

    /**
     * Test update by non-admin is forbidden.
     */
    public function test_non_admin_cannot_update_user(): void
    {
        $this->actingAs($this->doctor);

        $updateData = [
            'name' => 'Hacked Name',
            'email' => 'hacker@datapet.com',
            'role' => 'admin',
        ];

        $response = $this->put(route('users.update', $this->client), $updateData);

        $response->assertStatus(403);
    }

    /**
     * Test toggleStatus changes active status.
     */
    public function test_admin_can_toggle_user_status(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue((bool) $this->doctor->status);

        // Toggle to false
        $response = $this->patch(route('users.toggleStatus', $this->doctor));
        $response->assertRedirect(route('users.index'));
        $this->assertFalse((bool) $this->doctor->fresh()->status);

        // Toggle back to true
        $this->patch(route('users.toggleStatus', $this->doctor));
        $this->assertTrue((bool) $this->doctor->fresh()->status);
    }

    /**
     * Test destroy deletes user.
     */
    public function test_admin_can_delete_user(): void
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('users.destroy', $this->client));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $this->client->id]);
    }

    /**
     * Test destroy by non-admin is forbidden.
     */
    public function test_non_admin_cannot_delete_user(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->delete(route('users.destroy', $this->client));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $this->client->id]);
    }
}
