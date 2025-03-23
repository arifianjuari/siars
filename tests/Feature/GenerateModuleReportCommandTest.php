<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ModuleActivationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GenerateModuleReportCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $user = User::factory()->create(['name' => 'Test User']);
        $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
        $module = Module::factory()->create(['name' => 'Test Module']);

        // Create some logs
        ModuleActivationLog::factory()->count(10)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up any generated files
        $formats = ['csv', 'json', 'html'];
        foreach ($formats as $format) {
            $path = storage_path("app/reports/module_report.$format");
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        parent::tearDown();
    }

    /** @test */
    public function it_generates_csv_report()
    {
        // Execute the command
        $this->artisan('modules:report', [
            '--format' => 'csv',
            '--output' => storage_path('app/reports/module_report.csv')
        ])
            ->expectsOutput('Laporan aktivasi modul berhasil dibuat dalam format CSV.')
            ->assertExitCode(0);

        // Check that file exists
        $this->assertFileExists(storage_path('app/reports/module_report.csv'));

        // Check file contents
        $content = File::get(storage_path('app/reports/module_report.csv'));
        $this->assertStringContainsString('Module,Tenant,User,Action,Date', $content);
        $this->assertStringContainsString('Test Module,Test Tenant,Test User,activated,', $content);
    }

    /** @test */
    public function it_generates_json_report()
    {
        // Execute the command
        $this->artisan('modules:report', [
            '--format' => 'json',
            '--output' => storage_path('app/reports/module_report.json')
        ])
            ->expectsOutput('Laporan aktivasi modul berhasil dibuat dalam format JSON.')
            ->assertExitCode(0);

        // Check that file exists
        $this->assertFileExists(storage_path('app/reports/module_report.json'));

        // Check file contents
        $content = File::get(storage_path('app/reports/module_report.json'));
        $report = json_decode($content, true);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('data', $report);
        $this->assertArrayHasKey('stats', $report);
        $this->assertCount(10, $report['data']);
        $this->assertArrayHasKey('total_activated', $report['stats']);
    }

    /** @test */
    public function it_generates_html_report()
    {
        // Execute the command
        $this->artisan('modules:report', [
            '--format' => 'html',
            '--output' => storage_path('app/reports/module_report.html')
        ])
            ->expectsOutput('Laporan aktivasi modul berhasil dibuat dalam format HTML.')
            ->assertExitCode(0);

        // Check that file exists
        $this->assertFileExists(storage_path('app/reports/module_report.html'));

        // Check file contents
        $content = File::get(storage_path('app/reports/module_report.html'));
        $this->assertStringContainsString('<html', $content);
        $this->assertStringContainsString('<table', $content);
        $this->assertStringContainsString('Test Module', $content);
        $this->assertStringContainsString('Test Tenant', $content);
        $this->assertStringContainsString('Test User', $content);
        $this->assertStringContainsString('activated', $content);
    }

    /** @test */
    public function it_filters_by_date_range()
    {
        // Create logs with specific dates
        $user = User::factory()->first();
        $tenant = Tenant::factory()->first();
        $module = Module::factory()->first();

        // Older logs (should be filtered out)
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => '2024-01-01 00:00:00',
        ]);

        // Logs in the range (should be included)
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => '2024-03-15 00:00:00',
        ]);

        // Newer logs (should be filtered out)
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => '2024-04-01 00:00:00',
        ]);

        // Execute the command with date filters
        $this->artisan('modules:report', [
            '--format' => 'json',
            '--output' => storage_path('app/reports/module_report.json'),
            '--start-date' => '2024-03-01',
            '--end-date' => '2024-03-31'
        ])
            ->expectsOutput('Laporan aktivasi modul berhasil dibuat dalam format JSON.')
            ->assertExitCode(0);

        // Check file contents
        $content = File::get(storage_path('app/reports/module_report.json'));
        $report = json_decode($content, true);

        // Find the log that matches our date
        $found = false;
        foreach ($report['data'] as $log) {
            if (strpos($log['action_at'], '2024-03-15') !== false) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'The log with date 2024-03-15 should be in the report');

        // Make sure we don't have logs outside our date range
        $foundOutsideRange = false;
        foreach ($report['data'] as $log) {
            if (
                strpos($log['action_at'], '2024-01-01') !== false ||
                strpos($log['action_at'], '2024-04-01') !== false
            ) {
                $foundOutsideRange = true;
                break;
            }
        }
        $this->assertFalse($foundOutsideRange, 'Logs outside the date range should not be in the report');
    }

    /** @test */
    public function it_filters_by_tenant()
    {
        // Create a second tenant
        $tenant1 = Tenant::factory()->first();
        $tenant2 = Tenant::factory()->create(['name' => 'Second Tenant']);

        $user = User::factory()->first();
        $module = Module::factory()->first();

        // Create logs for second tenant
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant2->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => now(),
        ]);

        // Execute the command with tenant filter
        $this->artisan('modules:report', [
            '--format' => 'json',
            '--output' => storage_path('app/reports/module_report.json'),
            '--tenant' => $tenant2->id
        ])
            ->expectsOutput('Laporan aktivasi modul berhasil dibuat dalam format JSON.')
            ->assertExitCode(0);

        // Check file contents
        $content = File::get(storage_path('app/reports/module_report.json'));
        $report = json_decode($content, true);

        // All logs should be for the specified tenant
        foreach ($report['data'] as $log) {
            $this->assertEquals('Second Tenant', $log['tenant_name']);
        }

        // Ensure no logs from other tenant
        $foundOtherTenant = false;
        foreach ($report['data'] as $log) {
            if ($log['tenant_name'] === 'Test Tenant') {
                $foundOtherTenant = true;
                break;
            }
        }
        $this->assertFalse($foundOtherTenant, 'Logs from other tenants should not be in the report');
    }

    /** @test */
    public function it_filters_by_action()
    {
        $user = User::factory()->first();
        $tenant = Tenant::factory()->first();
        $module = Module::factory()->first();

        // Create logs with different actions
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'deactivated',
            'action_at' => now(),
        ]);

        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'requested',
            'action_at' => now(),
        ]);

        // Execute the command with action filter
        $this->artisan('modules:report', [
            '--format' => 'json',
            '--output' => storage_path('app/reports/module_report.json'),
            '--action' => 'deactivated'
        ])
            ->expectsOutput('Laporan aktivasi modul berhasil dibuat dalam format JSON.')
            ->assertExitCode(0);

        // Check file contents
        $content = File::get(storage_path('app/reports/module_report.json'));
        $report = json_decode($content, true);

        // All logs should have the specified action
        foreach ($report['data'] as $log) {
            $this->assertEquals('deactivated', $log['action']);
        }

        // Ensure no logs with other actions
        $foundOtherAction = false;
        foreach ($report['data'] as $log) {
            if ($log['action'] === 'activated' || $log['action'] === 'requested') {
                $foundOtherAction = true;
                break;
            }
        }
        $this->assertFalse($foundOtherAction, 'Logs with other actions should not be in the report');
    }
}
