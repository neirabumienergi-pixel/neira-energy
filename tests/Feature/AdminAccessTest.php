<?php

namespace Tests\Feature;

use App\Models\ProductPackage;
use App\Models\User;
use Database\Seeders\NeiraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(NeiraSeeder::class);
    }

    protected function as(string $role): static
    {
        return $this->actingAs(User::factory()->create(['role' => $role]));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_super_admin_can_open_every_module(): void
    {
        $this->as('super_admin');
        foreach (['/admin', '/admin/product-packages', '/admin/product-packages/1/edit', '/admin/product-categories/1/edit', '/admin/banners',
            '/admin/posts', '/admin/portfolios', '/admin/promos', '/admin/users', '/admin/company-settings'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_marketing_cannot_manage_users_or_settings(): void
    {
        $this->as('marketing');
        $this->get('/admin/posts')->assertOk();
        $this->get('/admin/product-packages/1/edit')->assertOk();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/company-settings')->assertForbidden();
    }

    public function test_sales_can_view_but_not_edit_products(): void
    {
        $this->as('sales');
        $this->get('/admin/product-packages')->assertOk();
        $this->get('/admin/product-packages/1/edit')->assertForbidden();
        $this->get('/admin/banners')->assertForbidden();
    }

    public function test_inactive_user_cannot_access_panel(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'super_admin', 'is_active' => false]));
        $this->get('/admin')->assertForbidden();
    }
}
