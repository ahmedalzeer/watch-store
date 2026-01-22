<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'vendor']);
        Role::create(['name' => 'customer']);

        // Create Admin User
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_view_users_index()
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users/Index')
            ->has('users.data')
        );
    }

    public function test_admin_can_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'phone' => '01012345678',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'vendor',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
        
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($user->hasRole('vendor'));
    }

    public function test_admin_can_update_user()
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $updateData = [
            'name' => 'Updated User',
            'email' => $user->email, // Keep email same
            'phone' => '01087654321',
            'role' => 'vendor', // Change role
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user->id), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'phone' => '01087654321',
        ]);

        $user->refresh();
        $this->assertTrue($user->hasRole('vendor'));
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
