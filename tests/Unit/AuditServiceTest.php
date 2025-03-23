<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantModule;
use App\Models\ModuleActivationLog;
use App\Models\ModuleActivationRequest;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditService $auditService;

    public function setUp(): void
    {
        parent::setUp();

        $this->auditService = new AuditService();

        // Set fixed time for testing
        Carbon::setTestNow('2024-03-25 10:00:00');
    }

    /** @test */
    public function it_logs_module_activation()
    {
        // Create test data
        $user = User::factory()->create(['name' => 'Admin User']);
        $module = Module::factory()->create(['name' => 'Test Module']);
        $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);

        // Log activation
        $log = $this->auditService->logModuleActivation(
            'activated',
            $module,
            $tenant,
            $user,
            null,
            'Aktivasi untuk pengujian',
            ['active' => false],
            ['active' => true]
        );

        // Assert result
        $this->assertInstanceOf(ModuleActivationLog::class, $log);
        $this->assertEquals('activated', $log->action);
        $this->assertEquals($module->id, $log->module_id);
        $this->assertEquals($tenant->id, $log->tenant_id);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals('Aktivasi untuk pengujian', $log->notes);
        $this->assertEquals(['active' => false], $log->old_data);
        $this->assertEquals(['active' => true], $log->new_data);

        // Verify data in database
        $this->assertDatabaseHas('module_activation_logs', [
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'notes' => 'Aktivasi untuk pengujian',
        ]);
    }

    /** @test */
    public function it_logs_module_activated()
    {
        // Create test data
        $user = User::factory()->create();
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Log activation
        $log = $this->auditService->logModuleActivated($module, $tenant, $user, 'Aktivasi modul');

        // Assert result
        $this->assertInstanceOf(ModuleActivationLog::class, $log);
        $this->assertEquals('activated', $log->action);
        $this->assertEquals(['active' => false], $log->old_data);
        $this->assertEquals(['active' => true], $log->new_data);

        // Verify data in database
        $this->assertDatabaseHas('module_activation_logs', [
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'notes' => 'Aktivasi modul',
        ]);
    }

    /** @test */
    public function it_logs_module_deactivated()
    {
        // Create test data
        $user = User::factory()->create();
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Log deactivation
        $log = $this->auditService->logModuleDeactivated($module, $tenant, $user, 'Deaktivasi modul');

        // Assert result
        $this->assertInstanceOf(ModuleActivationLog::class, $log);
        $this->assertEquals('deactivated', $log->action);
        $this->assertEquals(['active' => true], $log->old_data);
        $this->assertEquals(['active' => false], $log->new_data);

        // Verify data in database
        $this->assertDatabaseHas('module_activation_logs', [
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'deactivated',
            'notes' => 'Deaktivasi modul',
        ]);
    }

    /** @test */
    public function it_logs_activation_request()
    {
        // Create test data
        $user = User::factory()->create();
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
            'status' => 'pending',
            'notes' => 'Permintaan aktivasi',
            'requested_at' => now(),
        ]);

        // Link relationships for the request
        $request->module = $module;
        $request->tenant = $tenant;
        $request->requester = $user;

        // Log request
        $log = $this->auditService->logActivationRequest($request);

        // Assert result
        $this->assertInstanceOf(ModuleActivationLog::class, $log);
        $this->assertEquals('requested', $log->action);
        $this->assertEquals($request->id, $log->request_id);

        // Verify data in database
        $this->assertDatabaseHas('module_activation_logs', [
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'requested',
            'request_id' => $request->id,
        ]);
    }

    /** @test */
    public function it_gets_module_activation_history()
    {
        // Create test data
        $user = User::factory()->create();
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Create log entries
        ModuleActivationLog::factory()->count(3)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => now()->subDays(1),
        ]);

        // Create log for different tenant
        $otherTenant = Tenant::factory()->create();
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $otherTenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => now()->subDays(1),
        ]);

        // Get history for specific tenant
        $history = $this->auditService->getModuleActivationHistory($tenant);

        // Assert result
        $this->assertCount(3, $history);

        // All logs should be for the same tenant
        foreach ($history as $log) {
            $this->assertEquals($tenant->id, $log->tenant_id);
        }
    }

    /** @test */
    public function it_gets_module_activation_history_with_date_filter()
    {
        // Create test data
        $user = User::factory()->create();
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Create log entries with different dates
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => '2024-03-20 10:00:00',
        ]);

        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'deactivated',
            'action_at' => '2024-03-22 10:00:00',
        ]);

        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => '2024-03-25 10:00:00',
        ]);

        // Get history with date filter
        $history = $this->auditService->getModuleActivationHistory(
            $tenant,
            '2024-03-21',
            '2024-03-24'
        );

        // Assert result - should only include the middle entry
        $this->assertCount(1, $history);
        $this->assertEquals('deactivated', $history->first()->action);
        $this->assertEquals('2024-03-22 10:00:00', $history->first()->action_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_gets_active_modules_by_tenant()
    {
        // Create test data
        $user = User::factory()->create(['name' => 'Approval User']);
        $tenant = Tenant::factory()->create(['name' => 'Test Tenant', 'code' => 'TT']);

        // Create modules
        $module1 = Module::factory()->create(['name' => 'Module 1', 'code' => 'M1']);
        $module2 = Module::factory()->create(['name' => 'Module 2', 'code' => 'M2']);
        $module3 = Module::factory()->create(['name' => 'Module 3', 'code' => 'M3']);

        // Activate modules
        TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module1->id,
            'is_active' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module2->id,
            'is_active' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // This one is not active
        TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module3->id,
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Get active modules
        $activeModules = $this->auditService->getActiveModulesByTenant($tenant);

        // Assert result
        $this->assertCount(2, $activeModules);

        // Check first module
        $this->assertEquals($module1->id, $activeModules[0]->module_id);
        $this->assertEquals($module1->name, $activeModules[0]->module_name);
        $this->assertEquals($module1->code, $activeModules[0]->module_code);
        $this->assertEquals($tenant->id, $activeModules[0]->tenant_id);
        $this->assertEquals($tenant->name, $activeModules[0]->tenant_name);
        $this->assertEquals($tenant->code, $activeModules[0]->tenant_code);
        $this->assertEquals($user->name, $activeModules[0]->approved_by_name);

        // Check that inactive module is not included
        $inactiveModuleId = $module3->id;
        $activeModuleIds = $activeModules->pluck('module_id')->toArray();
        $this->assertNotContains($inactiveModuleId, $activeModuleIds);
    }

    /** @test */
    public function it_gets_module_activation_stats()
    {
        // Create test data
        $user = User::factory()->create();
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Create different types of logs
        // 2 activations
        ModuleActivationLog::factory()->count(2)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'activated',
            'action_at' => now(),
        ]);

        // 1 deactivation
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'deactivated',
            'action_at' => now(),
        ]);

        // 3 requests
        ModuleActivationLog::factory()->count(3)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'requested',
            'action_at' => now(),
        ]);

        // 2 approvals
        ModuleActivationLog::factory()->count(2)->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'approved',
            'action_at' => now(),
        ]);

        // 1 rejection
        ModuleActivationLog::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'rejected',
            'action_at' => now(),
        ]);

        // Get stats
        $stats = $this->auditService->getModuleActivationStats();

        // Assert result
        $this->assertEquals(2, $stats['total_activated']);
        $this->assertEquals(1, $stats['total_deactivated']);
        $this->assertEquals(3, $stats['total_requested']);
        $this->assertEquals(2, $stats['total_approved']);
        $this->assertEquals(1, $stats['total_rejected']);
        $this->assertEquals(66.67, $stats['acceptance_rate']);
    }

    /**
     * Reset the Carbon test instance after each test
     */
    public function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
