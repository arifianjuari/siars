<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ModuleActivationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $this->user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);

        // Assign roles
        $this->admin->assignRole('admin');

        // Create test data
        $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
        $module = Module::factory()->create(['name' => 'Test Module']);

        // Create module activation logs
        ModuleActivationLog::factory()->count(10)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $this->admin->id,
            'action' => 'activated',
            'action_at' => now()->subDays(1),
        ]);

        ModuleActivationLog::factory()->count(5)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $this->user->id,
            'action' => 'requested',
            'action_at' => now()->subDays(2),
        ]);

        ModuleActivationLog::factory()->count(2)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $this->admin->id,
            'action' => 'deactivated',
            'action_at' => now()->subDays(3),
        ]);
    }

    /** @test */
    public function admin_can_view_module_report_page()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Access the report page
        $response = $this->get(route('reports.modules'));

        // Assert successful response
        $response->assertStatus(200);
        $response->assertViewIs('reports.modules.index');

        // Check content
        $response->assertSee('Laporan Aktivasi Modul');
        $response->assertSee('Test Module');
        $response->assertSee('Test Tenant');
    }

    /** @test */
    public function regular_user_cannot_view_module_report_page()
    {
        // Login as regular user
        $this->actingAs($this->user);

        // Try to access the report page
        $response = $this->get(route('reports.modules'));

        // Assert forbidden
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_filter_report_by_date()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Access the report page with date filters
        $response = $this->get(route('reports.modules', [
            'start_date' => now()->subDays(2)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        // Assert successful response
        $response->assertStatus(200);

        // Older logs (from 3 days ago) should not be included
        $response->assertViewHas('history', function ($history) {
            // Should only include logs from the last 2 days
            return $history->count() === 15 && // 10 activated + 5 requested
                $history->where('action', 'deactivated')->count() === 0; // No deactivated logs
        });
    }

    /** @test */
    public function admin_can_filter_report_by_action()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Access the report page with action filter
        $response = $this->get(route('reports.modules', [
            'action' => 'activated'
        ]));

        // Assert successful response
        $response->assertStatus(200);

        // Only 'activated' logs should be included
        $response->assertViewHas('history', function ($history) {
            return $history->count() === 10 && // Only the 10 activated logs
                $history->where('action', 'activated')->count() === 10;
        });
    }

    /** @test */
    public function admin_can_filter_report_by_tenant()
    {
        // Create a second tenant and logs
        $tenant2 = Tenant::factory()->create(['name' => 'Second Tenant']);
        $module = Module::factory()->first();

        ModuleActivationLog::factory()->count(3)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant2->id,
            'user_id' => $this->admin->id,
            'action' => 'activated',
            'action_at' => now(),
        ]);

        // Login as admin
        $this->actingAs($this->admin);

        // Access the report page with tenant filter
        $response = $this->get(route('reports.modules', [
            'tenant_id' => $tenant2->id
        ]));

        // Assert successful response
        $response->assertStatus(200);

        // Only logs for the selected tenant should be included
        $response->assertViewHas('history', function ($history) use ($tenant2) {
            return $history->count() === 3 && // Only the 3 logs for the second tenant
                $history->where('tenant_id', $tenant2->id)->count() === 3;
        });
    }

    /** @test */
    public function admin_can_export_report_as_csv()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Request CSV export
        $response = $this->get(route('reports.modules.export', [
            'format' => 'csv'
        ]));

        // Assert successful response
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=module_activation_report.csv');
    }

    /** @test */
    public function admin_can_export_filtered_report()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Request filtered CSV export
        $response = $this->get(route('reports.modules.export', [
            'format' => 'csv',
            'action' => 'requested',
            'start_date' => now()->subDays(3)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        // Assert successful response
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Content should only contain 'requested' actions
        $content = $response->getContent();
        $this->assertStringContainsString('requested', $content);
        $this->assertStringNotContainsString('activated', $content);
        $this->assertStringNotContainsString('deactivated', $content);
    }

    /** @test */
    public function regular_user_cannot_export_report()
    {
        // Login as regular user
        $this->actingAs($this->user);

        // Try to export report
        $response = $this->get(route('reports.modules.export', [
            'format' => 'csv'
        ]));

        // Assert forbidden
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_active_modules_report()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Access the active modules report
        $response = $this->get(route('reports.modules.active'));

        // Assert successful response
        $response->assertStatus(200);
        $response->assertViewIs('reports.modules.active');

        // Check content
        $response->assertSee('Modul Aktif');
        $response->assertSee('Daftar Modul Aktif per Tenant');
    }

    /** @test */
    public function regular_user_cannot_view_active_modules_report()
    {
        // Login as regular user
        $this->actingAs($this->user);

        // Try to access the active modules report
        $response = $this->get(route('reports.modules.active'));

        // Assert forbidden
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_activation_stats()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Access the stats page
        $response = $this->get(route('reports.modules.stats'));

        // Assert successful response
        $response->assertStatus(200);
        $response->assertViewIs('reports.modules.stats');

        // Check content
        $response->assertSee('Statistik Aktivasi Modul');
        $response->assertViewHas('stats', function ($stats) {
            return isset($stats['total_activated']) &&
                isset($stats['total_requested']) &&
                isset($stats['total_deactivated']);
        });
    }

    /** @test */
    public function regular_user_cannot_view_activation_stats()
    {
        // Login as regular user
        $this->actingAs($this->user);

        // Try to access the stats page
        $response = $this->get(route('reports.modules.stats'));

        // Assert forbidden
        $response->assertStatus(403);
    }
}
