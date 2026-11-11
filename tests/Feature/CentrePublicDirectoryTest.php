<?php

namespace Tests\Feature;

use App\Models\Centre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CentrePublicDirectoryTest extends TestCase
{
    public function test_centres_index_displays_national_centres(): void
    {
        $response = $this->get('/centres');
        $response->assertStatus(200);
        $response->assertSee('Our National Classroom Centres');
        $response->assertSee('Delhi Old Rajinder Nagar');
        $response->assertSee('Delhi Mukherjee Nagar');
        $response->assertSee('Prayagraj');
        $response->assertSee('Jaipur');
        $response->assertSee('Pune');
    }

    public function test_centre_detail_page_loads_with_address_and_batches(): void
    {
        $centre = Centre::where('slug', 'delhi-old-rajinder-nagar')->firstOrFail();

        $response = $this->get("/centres/{$centre->slug}");
        $response->assertStatus(200);
        $response->assertSee($centre->name);
        $response->assertSee($centre->phone);
        $response->assertSee('Upcoming Classroom Batches');
    }

    public function test_centres_filter_by_city(): void
    {
        $response = $this->get('/centres?city=Jaipur');
        $response->assertStatus(200);
        $response->assertSee('Jaipur Campus');
    }

    public function test_hindi_centres_route_is_accessible(): void
    {
        $response = $this->get('/hindi/centres');
        $response->assertStatus(200);
    }
}
