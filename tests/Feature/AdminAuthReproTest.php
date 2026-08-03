<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class AdminAuthReproTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_admin_after_customer_login()
    {
        $role = Role::create(['name' => 'customer', 'display_name' => 'Customer']);
        $user = User::factory()->create(['name' => 'Test Customer', 'role_id' => $role->id]);

        // Step 1: Authenticate as customer via web guard (simulates /login POST)
        $this->actingAs($user, 'web');

        // Step 2: Guard check immediately after login
        $this->assertTrue(Auth::guard('web')->check(), 'Web guard MUST be authenticated');
        $this->assertFalse(Auth::guard('admin')->check(), 'Admin guard MUST NOT be authenticated after web login');

        // Step 3: Try to access /admin
        $response = $this->get('/admin');

        if ($response->isRedirect()) {
            $location = $response->headers->get('Location') ?? '';
            $this->assertStringContainsString(
                'admin/login',
                $location,
                "Customer MUST be redirected to /admin/login, got redirect to: $location"
            );
        } else {
            $this->fail(
                "Customer accessed /admin with status {$response->getStatusCode()} instead of being redirected."
            );
        }
    }
}
