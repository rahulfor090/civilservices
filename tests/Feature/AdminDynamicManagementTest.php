<?php

namespace Tests\Feature;

use App\Models\HomepageSlot;
use App\Models\HomepageSlotItem;
use App\Models\Language;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AdminDynamicManagementTest extends TestCase
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::whereHas('roles', fn($q) => $q->where('slug', 'super_admin'))->first()
            ?? User::factory()->create();

        $adminRole = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Administrator']);
        if (!$this->adminUser->roles->contains($adminRole->id)) {
            $this->adminUser->roles()->attach($adminRole->id);
        }
    }

    public function test_admin_navigation_index_loads(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/navigation');
        $response->assertStatus(200);
        $response->assertSee('Mega-Menu Management');
    }

    public function test_admin_can_create_and_delete_navigation_items(): void
    {
        $lang = Language::where('code', 'en')->first() ?? Language::first();
        $menu = NavigationMenu::firstOrCreate(
            ['code' => 'mega_menu', 'language_id' => $lang->id],
            ['name' => 'English Primary Mega Menu', 'is_active' => true]
        );

        // 1. Create parent column
        $response = $this->actingAs($this->adminUser)->post('/admin/navigation/items', [
            'menu_id' => $menu->id,
            'label' => 'Test Interview Section',
            'url' => '#',
            'sort_order' => 99,
            'is_active' => 1,
        ]);
        $response->assertRedirect();

        $parent = NavigationItem::where('label', 'Test Interview Section')->first();
        $this->assertNotNull($parent);

        // 2. Create child link under column
        $responseChild = $this->actingAs($this->adminUser)->post('/admin/navigation/items', [
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'label' => 'Mock Interviews 2026',
            'url' => '/interview-guidance',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $responseChild->assertRedirect();

        $child = NavigationItem::where('label', 'Mock Interviews 2026')->first();
        $this->assertNotNull($child);

        // 3. Delete parent (should cascade delete child)
        $responseDelete = $this->actingAs($this->adminUser)->delete('/admin/navigation/items/' . $parent->id);
        $responseDelete->assertRedirect();

        $this->assertNull(NavigationItem::find($parent->id));
        $this->assertNull(NavigationItem::find($child->id));
    }

    public function test_admin_homepage_slots_index_loads(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/homepage-slots');
        $response->assertStatus(200);
        $response->assertSee('Homepage Announcements');
    }

    public function test_admin_can_create_and_manage_ticker_announcements(): void
    {
        $lang = Language::where('code', 'en')->first() ?? Language::first();
        $slot = HomepageSlot::firstOrCreate(
            ['language_id' => $lang->id, 'key' => 'announcement_ticker'],
            ['title' => 'This Just In Ticker', 'slot_type' => 'ticker', 'sort_order' => 1, 'is_active' => true]
        );

        // Create announcement
        $response = $this->actingAs($this->adminUser)->post('/admin/homepage-slots/items', [
            'slot_id' => $slot->id,
            'headline' => 'Test Flash Breaking News Notification',
            'cta_url' => '/daily-updates/daily-news-analysis',
            'sort_order' => 50,
            'is_active' => 1,
        ]);
        $response->assertRedirect();

        $item = HomepageSlotItem::where('headline', 'Test Flash Breaking News Notification')->first();
        $this->assertNotNull($item);

        // Update announcement
        $responseUpdate = $this->actingAs($this->adminUser)->put('/admin/homepage-slots/items/' . $item->id, [
            'headline' => 'Updated Flash Breaking News Notification',
            'cta_url' => '/mind-map',
            'sort_order' => 51,
            'is_active' => 1,
        ]);
        $responseUpdate->assertRedirect();

        $this->assertEquals('Updated Flash Breaking News Notification', $item->fresh()->headline);

        // Delete announcement
        $responseDelete = $this->actingAs($this->adminUser)->delete('/admin/homepage-slots/items/' . $item->id);
        $responseDelete->assertRedirect();

        $this->assertNull(HomepageSlotItem::find($item->id));
    }
}
