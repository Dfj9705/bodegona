<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Movement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class JsonEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
    }

    public function test_admin_can_list_brands_as_json(): void
    {
        $admin = $this->createAdminUser();
        $brands = Brand::factory()->count(2)->create();

        $this->actingAs($admin)
            ->getJson('/brands')
            ->assertOk()
            ->assertJsonCount(2, 'brands')
            ->assertJsonFragment(['name' => $brands->first()->name]);
    }

    public function test_admin_can_create_brand_through_json_endpoint(): void
    {
        $admin = $this->createAdminUser();

        $payload = ['name' => 'Marca Nueva'];

        $this->actingAs($admin)
            ->postJson('/brands', $payload)
            ->assertOk()
            ->assertJsonPath('data.name', 'Marca Nueva');

        $this->assertDatabaseHas('brands', ['name' => 'Marca Nueva']);
    }

    public function test_admin_can_list_users_with_roles_as_json(): void
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->getJson('/users')
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_authenticated_user_can_list_products_with_brand_names(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();
        $product = Product::factory()->for($brand)->create();

        $this->actingAs($user)
            ->getJson('/products')
            ->assertOk()
            ->assertJsonCount(1, 'products')
            ->assertJsonFragment([
                'id' => $product->id,
                'brand_name' => $brand->name,
            ]);
    }

    public function test_authenticated_user_can_list_movements_summary(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();
        $product = Product::factory()->for($brand)->create();

        Movement::create([
            'product_id' => $product->id,
            'type' => 1,
            'amount' => 5,
            'date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        Movement::create([
            'product_id' => $product->id,
            'type' => 2,
            'amount' => 2,
            'date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->getJson('/movements')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => $product->name,
            ])
            ->assertJsonFragment([
                'ingresos' => 5,
                'egresos' => 2,
            ]);
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('administrador');

        return $user;
    }
}
