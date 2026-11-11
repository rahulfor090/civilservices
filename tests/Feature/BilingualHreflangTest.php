<?php

namespace Tests\Feature;

use App\Models\Article;
use Tests\TestCase;

class BilingualHreflangTest extends TestCase
{
    /**
     * Test that English and Hindi home pages return status 200 and reciprocal hreflang tags.
     */
    public function test_homepages_render_bilingual_hreflang_tags(): void
    {
        $enResponse = $this->get('/');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('hreflang="en-in"', false);
        $enResponse->assertSee('hreflang="hi-in"', false);
        $enResponse->assertSee('hreflang="x-default"', false);

        $hiResponse = $this->get('/hindi');
        $hiResponse->assertStatus(200);
        $hiResponse->assertSee('hreflang="en-in"', false);
        $hiResponse->assertSee('hreflang="hi-in"', false);
    }

    /**
     * Test that an article detail page links to its reciprocal Hindi/English twin.
     */
    public function test_article_detail_shows_reciprocal_translation_twin(): void
    {
        $enArticle = Article::published()
            ->whereHas('language', fn($q) => $q->where('code', 'en'))
            ->whereNotNull('translation_group_id')
            ->whereIn('translation_group_id', function ($q) {
                $q->select('translation_group_id')
                    ->from('articles')
                    ->groupBy('translation_group_id')
                    ->havingRaw('COUNT(*) > 1');
            })
            ->first();

        $this->assertNotNull($enArticle);

        $twin = $enArticle->translationTwin();
        $this->assertNotNull($twin, 'Seeded article should have a corresponding Hindi twin.');

        $response = $this->get($enArticle->url);
        $response->assertStatus(200);
        $response->assertSee($twin->url, false);
    }
}
