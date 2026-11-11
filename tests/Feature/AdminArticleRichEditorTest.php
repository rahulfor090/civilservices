<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ContentType;
use App\Models\Language;
use App\Models\User;
use Tests\TestCase;

class AdminArticleRichEditorTest extends TestCase
{
    protected User $admin;
    protected Language $english;
    protected ContentType $contentType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@civilservicegurukul.com')->first();
        $this->english = Language::where('code', 'en')->first();
        $this->contentType = ContentType::first();
    }

    public function test_admin_article_create_page_renders_rich_html_editor_toolbar(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/articles/create');

        $response->assertStatus(200);
        $response->assertSee('Article Body Content (HTML / Structured Sections)');
        $response->assertSee('quill-toolbar', false);
        $response->assertSee('quill-editor', false);
        $response->assertSee('ql-bold', false);
        $response->assertSee('ql-italic', false);
        $response->assertSee('ql-underline', false);
        $response->assertSee('ql-color', false);
        $response->assertSee('ql-background', false);
        $response->assertSee('ql-align', false);
        $response->assertSee('Why in News?', false);
        $response->assertSee('Key Points', false);
        $response->assertSee('Way Forward', false);
        $response->assertSee('Mains Practice Question Box', false);
        $response->assertSee('Raw HTML Source', false);
        $response->assertSee('cdn.jsdelivr.net/npm/quill', false);
    }

    public function test_admin_article_edit_page_renders_rich_html_editor_with_existing_content(): void
    {
        $article = Article::first();
        $this->assertNotNull($article, 'At least one article should exist in database.');

        $response = $this->actingAs($this->admin)->get("/admin/articles/{$article->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('quill-editor', false);
        $response->assertSee('quill-toolbar', false);
        $response->assertSee('body_html_real_input', false);
        $response->assertSee('ql-color', false);
        $response->assertSee('ql-background', false);
    }

    public function test_article_creation_with_rich_html_formatting_is_persisted(): void
    {
        $richHtml = '<h2>Why in News?</h2><p>The <strong style="color: #0b1b3d;">Supreme Court</strong> ruled on constitutional benchmarking with <span style="background-color: #fef08a;">unanimous consensus</span>.</p><p><em>Italic dimension</em> and <u>underlined key principle</u>.</p><ul><li>Key reform point 1</li><li>Key reform point 2</li></ul>';

        $uniqueSlug = 'rich-editor-test-' . time();
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'language_id' => $this->english->id,
            'content_type_id' => $this->contentType->id,
            'title' => 'Rich Editor Test ' . time(),
            'slug' => $uniqueSlug,
            'body_html' => $richHtml,
            'status' => 'draft',
            'reading_minutes' => 6,
        ]);

        $response->assertRedirect('/admin/articles');

        $created = Article::where('slug', $uniqueSlug)->first();
        $this->assertNotNull($created);
        $this->assertStringContainsString('color: #0b1b3d;', $created->body_html);
        $this->assertStringContainsString('background-color: #fef08a;', $created->body_html);
        $this->assertStringContainsString('<em>Italic dimension</em>', $created->body_html);
        $this->assertStringContainsString('<u>underlined key principle</u>', $created->body_html);
    }
}
