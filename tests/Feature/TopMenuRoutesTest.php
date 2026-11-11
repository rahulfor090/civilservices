<?php

namespace Tests\Feature;

use Tests\TestCase;

class TopMenuRoutesTest extends TestCase
{
    public function test_free_downloads_loads_successfully(): void
    {
        $response = $this->get('/free-downloads');
        $response->assertStatus(200);
        $response->assertSee('Download');
    }

    public function test_mind_map_loads_successfully(): void
    {
        $response = $this->get('/mind-map');
        $response->assertStatus(200);
        $response->assertSee('Mind Maps');
    }

    public function test_state_pcs_loads_successfully(): void
    {
        $response = $this->get('/state-pcs-current-affairs');
        $response->assertStatus(200);
        $response->assertSee('State PCS');
    }

    public function test_daily_news_editorials_loads_successfully(): void
    {
        $response = $this->get('/daily-news-editorials');
        $response->assertStatus(200);
        $response->assertSee('Daily Editorial');
    }

    public function test_hindi_top_menu_routes_load_successfully(): void
    {
        $this->get('/hindi/free-downloads')->assertStatus(200);
        $this->get('/hindi/mind-map')->assertStatus(200);
        $this->get('/hindi/state-pcs-current-affairs')->assertStatus(200);
        $this->get('/hindi/daily-news-editorials')->assertStatus(200);
    }

    public function test_mind_map_article_detail_loads_successfully(): void
    {
        $response = $this->get('/mind-map/mind-map-fundamental-rights-vs-dpsp');
        $response->assertStatus(200);
        $response->assertSee('Fundamental Rights vs Directive Principles');

        $responseHi = $this->get('/hindi/mind-map/mind-map-fundamental-rights-vs-dpsp');
        $responseHi->assertStatus(200);
        $responseHi->assertSee('मूल अधिकार बनाम राज्य के नीति निर्देशक तत्व');
    }
}
