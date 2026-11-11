<?php

namespace Tests\Feature;

use App\Models\Article;
use Tests\TestCase;

class ArticlePublicViewTest extends TestCase
{
    /**
     * Test that the articles index page loads with status 200 and renders numbering.
     */
    public function test_articles_index_loads_successfully(): void
    {
        $response = $this->get('/daily-updates/daily-news-analysis');

        $response->assertStatus(200);
        $response->assertSee('Daily News Analysis');
        $response->assertSee('01.');
    }

    /**
     * Test that an article detail page loads with question-style sections and TOC.
     */
    public function test_article_detail_page_loads_with_structured_sections(): void
    {
        $article = Article::published()->has('sections')->whereHas('language', fn($q) => $q->where('code', 'en'))->first();

        $this->assertNotNull($article, 'A published English article with sections should exist from seeders.');

        $response = $this->get($article->url);

        $response->assertStatus(200);
        $response->assertSee($article->title);
        $response->assertSee('Table of Contents');
        $response->assertSee('Previous Year Question');
    }

    /**
     * Test that non-existent article slug returns 404.
     */
    public function test_non_existent_article_returns_404(): void
    {
        $response = $this->get('/daily-updates/daily-news-analysis/completely-invalid-slug-12345');

        $response->assertStatus(404);
    }
}
