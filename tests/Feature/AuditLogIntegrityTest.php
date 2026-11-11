<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\AuditLog;
use App\Models\ContentType;
use App\Models\Language;
use App\Models\User;
use Tests\TestCase;

class AuditLogIntegrityTest extends TestCase
{
    /**
     * Test that mutating content creates a tamper-evident audit record with SHA-256 hash chaining.
     */
    public function test_article_creation_records_tamper_evident_audit_log(): void
    {
        $admin = User::where('email', 'admin@civilservicegurukul.com')->first();
        $lang = Language::where('code', 'en')->first();
        $type = ContentType::first();

        $lastLogBefore = AuditLog::latest('id')->first();
        $expectedPrevHash = $lastLogBefore ? $lastLogBefore->hash : 'GENESIS_BLOCK_CIVILSERVICEGURUKUL';

        $response = $this->actingAs($admin)->post('/admin/articles', [
            'language_id' => $lang->id,
            'content_type_id' => $type->id,
            'title' => 'Audit Trail Verification Article ' . time(),
            'slug' => 'audit-trail-verification-' . time(),
            'body_html' => '<h2>Test Section</h2><p>Verifying cryptographic hash chaining.</p>',
            'status' => 'published',
            'reading_minutes' => 4,
        ]);

        $response->assertRedirect('/admin/articles');

        $latestLog = AuditLog::latest('id')->first();
        $this->assertNotNull($latestLog);
        $this->assertEquals('create_article', $latestLog->action);
        $this->assertEquals($admin->id, $latestLog->user_id);
        $this->assertEquals($expectedPrevHash, $latestLog->previous_hash);

        // Verify SHA-256 format (64 hex characters)
        $this->assertEquals(64, strlen($latestLog->record_hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $latestLog->record_hash);
    }
}
