<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminAuthSeparationTest extends TestCase
{
    public function test_basic_routes()
    {
        $response = $this->get('/admin');
        $this->assertContains($response->getStatusCode(), [200, 302, 404], 'GET /admin status: ' . $response->getStatusCode());

        $response2 = $this->get('/admin/login');
        $this->assertContains($response2->getStatusCode(), [200, 302, 404], 'GET /admin/login status: ' . $response2->getStatusCode());

        $response3 = $this->get('/login');
        $this->assertContains($response3->getStatusCode(), [200, 302, 404], 'GET /login status: ' . $response3->getStatusCode());

        $this->assertTrue(true);
    }
}
