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
}
