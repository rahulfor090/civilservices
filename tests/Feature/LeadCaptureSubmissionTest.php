<?php

namespace Tests\Feature;

use App\Models\Lead;
use Tests\TestCase;

class LeadCaptureSubmissionTest extends TestCase
{
    /**
     * Test successful lead capture via form submission.
     */
    public function test_valid_lead_submission_is_persisted(): void
    {
        $payload = [
            'first_name' => 'Aditi',
            'last_name' => 'Sharma',
            'email' => 'aditi.test.' . time() . '@example.com',
            'phone' => '9876543210',
            'interest' => 'UPSC Civil Services',
            'consent' => '1',
            'utm_source' => 'google_ads',
            'utm_campaign' => 'prelims_2026',
        ];

        $response = $this->postJson('/leads/capture', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('leads', [
            'email' => $payload['email'],
            'first_name' => 'Aditi',
            'status' => 'new',
        ]);
    }

    /**
     * Test that bot submissions filling the honeypot field are ignored.
     */
    public function test_honeypot_rejects_bot_submissions(): void
    {
        $botPayload = [
            'first_name' => 'Spambot',
            'email' => 'bot.' . time() . '@spamdomain.com',
            'phone' => '1234567890',
            'consent' => '1',
            'hp_extra' => 'http://spam-link.com', // Honeypot filled!
        ];

        $response = $this->postJson('/leads/capture', $botPayload);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('leads', [
            'email' => $botPayload['email'],
        ]);
    }
}
