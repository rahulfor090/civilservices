<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminRbacAccessTest extends TestCase
{
    /**
     * Test that guests cannot access the admin area.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test that a student account cannot access staff admin area (403 Forbidden).
     */
    public function test_student_role_receives_403_forbidden(): void
    {
        $student = User::where('email', 'student@civilservicegurukul.com')->first();
        $this->assertNotNull($student, 'Demo student must exist.');

        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test that an admin user can access the admin dashboard.
     */
    public function test_admin_user_can_access_dashboard(): void
    {
        $admin = User::where('email', 'admin@civilservicegurukul.com')->first();
        $this->assertNotNull($admin, 'Demo admin must exist.');

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Editorial Dashboard');
    }
}
