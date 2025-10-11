<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_is_accessible(): void
    {
        $this->get(route('welcome'))
            ->assertOk()
            ->assertViewIs('welcome');
    }

    public function test_admin_can_view_brand_page(): void
    {
        Role::create(['name' => 'administrador', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('administrador');

        $this->actingAs($user)
            ->get(route('brand.view'))
            ->assertOk()
            ->assertViewIs('brands.index');
    }

    public function test_non_admin_user_is_redirected_from_brand_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('brand.view'))
            ->assertRedirect(route('welcome'))
            ->assertSessionHas('error', 'No tienes permiso para acceder a esta página.');
    }

    public function test_authenticated_user_can_view_movement_page_with_products(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();
        $product = Product::factory()->for($brand)->create();

        $this->actingAs($user)
            ->get(route('movements.view'))
            ->assertOk()
            ->assertViewIs('movement.index')
            ->assertViewHas('products', function ($products) use ($product) {
                return $products->contains($product);
            });
    }

    public function test_admin_can_view_user_page_with_roles(): void
    {
        $adminRole = Role::create(['name' => 'administrador', 'guard_name' => 'web']);
        $sellerRole = Role::create(['name' => 'vendedor', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole($adminRole);

        $this->actingAs($user)
            ->get(route('user.view'))
            ->assertOk()
            ->assertViewIs('users/index')
            ->assertViewHas('roles', function ($roles) use ($adminRole, $sellerRole) {
                return $roles->contains($adminRole) && $roles->contains($sellerRole);
            });
    }

    public function test_admin_can_view_product_page_with_brands(): void
    {
        Role::create(['name' => 'administrador', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('administrador');

        $brand = Brand::factory()->create();

        $this->actingAs($user)
            ->get(route('products.view'))
            ->assertOk()
            ->assertViewIs('products/index')
            ->assertViewHas('brands', function ($brands) use ($brand) {
                return $brands->contains($brand);
            });
    }

    public function test_admin_can_view_chart_page(): void
    {
        Role::create(['name' => 'administrador', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('administrador');

        $this->actingAs($user)
            ->get(route('chart.view'))
            ->assertOk()
            ->assertViewIs('chart/index');
    }

    public function test_non_admin_user_is_redirected_from_user_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('user.view'))
            ->assertRedirect(route('welcome'))
            ->assertSessionHas('error', 'No tienes permiso para acceder a esta página.');
    }

    public function test_non_admin_user_is_redirected_from_product_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('products.view'))
            ->assertRedirect(route('welcome'))
            ->assertSessionHas('error', 'No tienes permiso para acceder a esta página.');
    }

    public function test_guest_is_redirected_from_movement_page_to_login(): void
    {
        $this->get(route('movements.view'))
            ->assertRedirect(route('login'));
    }
}
