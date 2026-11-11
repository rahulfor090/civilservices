<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AdminCentreManagementTest extends TestCase
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::whereHas('roles', fn ($q) => $q->where('slug', 'super_admin'))->first()
            ?? User::factory()->create();

        $adminRole = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Administrator']);
        if (!$this->adminUser->roles->contains($adminRole->id)) {
            $this->adminUser->roles()->attach($adminRole->id);
        }
    }

    public function test_guest_and_student_cannot_access_admin_centres(): void
    {
        $this->get('/admin/centres')->assertRedirect('/login');

        $student = User::where('email', 'student@civilservicegurukul.com')->first();
        if ($student) {
            $this->actingAs($student)->get('/admin/centres')->assertStatus(403);
        }
    }

    public function test_admin_centres_index_and_edit_pages_load(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/centres');
        $response->assertStatus(200);
        $response->assertSee('National Centres');

        $hq = Centre::where('slug', 'delhi-old-rajinder-nagar')->firstOrFail();
        $editResponse = $this->actingAs($this->adminUser)->get("/admin/centres/{$hq->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee($hq->address);
    }

    public function test_admin_can_create_and_delete_centre(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/centres', [
            'name' => 'Bhopal Test Campus',
            'slug' => 'bhopal-test-campus',
            'city' => 'Bhopal',
            'state' => 'Madhya Pradesh',
            'address' => 'Plot 99, MP Nagar Zone II, Bhopal',
            'pincode' => '462011',
            'phone' => '+91-755-4001234',
            'email' => 'bhopal@civilservicegurukul.com',
            'google_maps_url' => 'https://maps.google.com/?q=MP+Nagar+Bhopal',
            'facilities_text' => "AC Classrooms\nStudy Library",
            'sort_order' => 99,
            'is_active' => 1,
        ]);

        $response->assertRedirect('/admin/centres');

        $centre = Centre::where('slug', 'bhopal-test-campus')->first();
        $this->assertNotNull($centre);
        $this->assertEquals('Plot 99, MP Nagar Zone II, Bhopal', $centre->address);
        $this->assertSame(['AC Classrooms', 'Study Library'], $centre->facilities);

        try {
            $this->get('/centres')->assertStatus(200)->assertSee('Plot 99, MP Nagar Zone II, Bhopal');
        } finally {
            $deleteResponse = $this->actingAs($this->adminUser)->delete("/admin/centres/{$centre->id}");
            $deleteResponse->assertRedirect('/admin/centres');
            $this->assertNull(Centre::find($centre->id));
        }
    }

    public function test_updating_hq_address_updates_public_views_and_logs_audit(): void
    {
        $hq = Centre::where('slug', 'delhi-old-rajinder-nagar')->firstOrFail();
        $original = [
            'name' => $hq->name,
            'slug' => $hq->slug,
            'city' => $hq->city,
            'state' => $hq->state,
            'address' => $hq->address,
            'pincode' => $hq->pincode,
            'phone' => $hq->phone,
            'email' => $hq->email,
            'google_maps_url' => $hq->google_maps_url,
            'latitude' => $hq->latitude,
            'longitude' => $hq->longitude,
            'facilities' => $hq->facilities,
            'sort_order' => $hq->sort_order,
            'is_active' => $hq->is_active,
        ];

        $tempAddress = '88, Bada Bazar Marg, Old Rajinder Nagar, New Delhi';

        try {
            $response = $this->actingAs($this->adminUser)->put("/admin/centres/{$hq->id}", [
                'name' => $hq->name,
                'slug' => $hq->slug,
                'city' => $hq->city,
                'state' => $hq->state,
                'address' => $tempAddress,
                'pincode' => $hq->pincode,
                'phone' => $hq->phone,
                'email' => $hq->email,
                'google_maps_url' => $hq->google_maps_url,
                'latitude' => $hq->latitude,
                'longitude' => $hq->longitude,
                'facilities_text' => is_array($hq->facilities) ? implode("\n", $hq->facilities) : '',
                'sort_order' => $hq->sort_order,
                'is_active' => 1,
            ]);

            $response->assertRedirect('/admin/centres');
            $this->assertEquals($tempAddress, $hq->fresh()->address);

            // Verify public pages and schema reflect the updated address
            $this->get('/')->assertStatus(200)->assertSee($tempAddress);
            $this->get('/centres')->assertStatus(200)->assertSee($tempAddress);
            $this->get("/centres/{$hq->slug}")->assertStatus(200)->assertSee($tempAddress);

            // Verify audit log recorded
            $audit = AuditLog::where('module', 'centre')
                ->where('action', 'update_centre')
                ->where('auditable_id', $hq->id)
                ->latest('id')
                ->first();
            $this->assertNotNull($audit);
        } finally {
            $hq->update($original);
        }
    }
}

