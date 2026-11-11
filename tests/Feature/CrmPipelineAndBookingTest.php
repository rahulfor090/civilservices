<?php

namespace Tests\Feature;

use App\Models\Centre;
use App\Models\Lead;
use App\Models\User;
use Tests\TestCase;

class CrmPipelineAndBookingTest extends TestCase
{
    public function test_counselling_booking_persists_and_syncs_to_crm_lead(): void
    {
        $centre = Centre::where('slug', 'delhi-old-rajinder-nagar')->firstOrFail();

        $response = $this->post('/book-counselling', [
            'full_name' => 'Vikram Singhania',
            'mobile' => '9988112233',
            'email' => 'vikram.singhania@gmail.com',
            'centre_id' => $centre->id,
            'medium' => 'en',
            'call_time_window' => 'afternoon',
            'target_year' => '2026',
            'interest_category' => 'UPSC Foundation',
            'notes' => 'Looking for Karol Bagh offline weekend batch.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('counselling_bookings', [
            'full_name' => 'Vikram Singhania',
            'mobile' => '9988112233',
            'centre_id' => $centre->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('leads', [
            'mobile' => '9988112233',
            'source' => 'counselling_booking',
            'status' => 'new',
        ]);
    }

    public function test_interview_guidance_booking_persists_and_syncs_to_lead(): void
    {
        $centre = Centre::where('slug', 'delhi-old-rajinder-nagar')->firstOrFail();

        $response = $this->post('/interview-guidance', [
            'full_name' => 'Priyanka Sen',
            'mobile' => '9988445566',
            'email' => 'priyanka.sen@gmail.com',
            'upsc_roll_number' => '0876543',
            'optional_subject' => 'PSIR',
            'medium' => 'en',
            'centre_id' => $centre->id,
            'notes' => 'Appeared for Mains 2024.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('interview_guidance_bookings', [
            'upsc_roll_number' => '0876543',
            'optional_subject' => 'PSIR',
            'status' => 'registered',
        ]);

        $this->assertDatabaseHas('leads', [
            'mobile' => '9988445566',
            'source' => 'interview_guidance',
        ]);
    }

    public function test_admin_can_update_lead_stage_and_assign_counsellor(): void
    {
        $admin = User::where('email', 'admin@civilservicegurukul.com')->firstOrFail();
        $counsellor = User::where('email', 'counsellor@civilservicegurukul.com')->firstOrFail();
        $lead = Lead::where('mobile', '9988112233')->firstOrFail();

        // 1. Assign to counsellor
        $assignResponse = $this->actingAs($admin)->post("/admin/crm/{$lead->id}/assign", [
            'assigned_to' => $counsellor->id,
        ]);
        $assignResponse->assertSessionHas('success');

        $lead->refresh();
        $this->assertEquals($counsellor->id, $lead->assigned_to);

        // 2. Advance stage to contacted
        $stageResponse = $this->actingAs($admin)->post("/admin/crm/{$lead->id}/stage", [
            'status' => 'contacted',
            'notes' => 'Spoke to candidate, scheduled offline visit to Karol Bagh.',
        ]);
        $stageResponse->assertSessionHas('success');

        $lead->refresh();
        $this->assertEquals('contacted', $lead->status);
    }

    public function test_admin_can_export_leads_and_quiz_csv(): void
    {
        $admin = User::where('email', 'admin@civilservicegurukul.com')->firstOrFail();

        // Leads CSV export
        $leadsExport = $this->actingAs($admin)->get('/admin/reports/export/leads');
        $leadsExport->assertStatus(200);
        $this->assertStringContainsString('text/csv', $leadsExport->headers->get('Content-Type'));

        // Quiz Performance CSV export
        $quizExport = $this->actingAs($admin)->get('/admin/reports/export/quizzes');
        $quizExport->assertStatus(200);
        $this->assertStringContainsString('text/csv', $quizExport->headers->get('Content-Type'));
    }
}
