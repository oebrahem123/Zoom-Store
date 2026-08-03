<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRedirectTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_page_preserves_redirect_to_in_form(): void
    {
        $response = $this->get('/login?redirect_to=/design/5');

        $response->assertStatus(200);
        $response->assertSee('<input type="hidden" name="redirect_to" value="/design/5">', false);
    }

    public function test_login_page_passes_redirect_to_to_register_link(): void
    {
        $response = $this->get('/login?redirect_to=/design/5');

        $response->assertStatus(200);
        $response->assertSee(route('register', ['redirect_to' => '/design/5']), false);
    }

    public function test_login_page_without_redirect_to_shows_normal_register_link(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee(route('register'), false);
        $response->assertDontSee('redirect_to=', false);
    }

    public function test_register_page_preserves_redirect_to_in_form(): void
    {
        $response = $this->get('/register?redirect_to=/design/5');

        $response->assertStatus(200);
        $response->assertSee('<input type="hidden" name="redirect_to" value="/design/5">', false);
    }

    public function test_register_page_passes_redirect_to_to_login_link(): void
    {
        $response = $this->get('/register?redirect_to=/design/5');

        $response->assertStatus(200);
        $response->assertSee(route('login', ['redirect_to' => '/design/5']), false);
    }
}
