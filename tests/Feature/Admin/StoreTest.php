<?php

namespace Tests\Feature\Admin;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $currency;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::factory()->create();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'vendor']); // Needed for store creation controller logic (users dropdown)
        $this->admin->assignRole('admin');

        // Ensure Currency exists
        $this->currency = \App\Models\Currency::create([
            'name' => ['en' => 'USD', 'ar' => 'USD'],
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_stores_index()
    {
        Store::factory()->create([
            'user_id' => $this->admin->id,
            'currency_id' => $this->currency->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.stores.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Stores/Index')
            ->has('stores.data')
        );
    }

    public function test_admin_can_create_store()
    {
        // Create a vendor user to assign the store to
        $vendor = User::factory()->create();
        $vendor->assignRole('vendor');

        $storeData = [
            'name' => ['en' => 'New Store', 'ar' => 'متجر جديد'],
            'subdomain' => 'new-store',
            'user_id' => $vendor->id,
            'currency_id' => $this->currency->id,
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.stores.store'), $storeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('stores', [
            'subdomain' => 'new-store',
            'user_id' => $vendor->id,
        ]);
    }

    public function test_admin_can_update_store()
    {
        $store = Store::factory()->create([
            'user_id' => $this->admin->id,
            'currency_id' => $this->currency->id
        ]);

        $updateData = [
            'name' => ['en' => 'Updated Store', 'ar' => 'متجر محدث'],
            'subdomain' => 'updated-store',
            'user_id' => $this->admin->id, // Keep same user
            'currency_id' => $this->currency->id,
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'is_active' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.stores.update', $store->id), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'subdomain' => 'updated-store',
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_delete_store()
    {
        $store = Store::factory()->create([
            'user_id' => $this->admin->id,
            'currency_id' => $this->currency->id
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.stores.destroy', $store->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }
}
