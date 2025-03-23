<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_tenant()
    {
        // Create test data
        $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
        $module = Module::factory()->create();
        $user = User::factory()->create();

        // Create tenant module relationship
        $tenantModule = TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Assert relationship
        $this->assertInstanceOf(Tenant::class, $tenantModule->tenant);
        $this->assertEquals($tenant->id, $tenantModule->tenant->id);
        $this->assertEquals('Test Tenant', $tenantModule->tenant->name);
    }

    /** @test */
    public function it_belongs_to_module()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $module = Module::factory()->create(['name' => 'Test Module']);
        $user = User::factory()->create();

        // Create tenant module relationship
        $tenantModule = TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Assert relationship
        $this->assertInstanceOf(Module::class, $tenantModule->module);
        $this->assertEquals($module->id, $tenantModule->module->id);
        $this->assertEquals('Test Module', $tenantModule->module->name);
    }

    /** @test */
    public function it_belongs_to_approver()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $module = Module::factory()->create();
        $user = User::factory()->create(['name' => 'Approver User']);

        // Create tenant module relationship
        $tenantModule = TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Assert relationship
        $this->assertInstanceOf(User::class, $tenantModule->approver);
        $this->assertEquals($user->id, $tenantModule->approver->id);
        $this->assertEquals('Approver User', $tenantModule->approver->name);
    }

    /** @test */
    public function it_has_active_scope()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $module1 = Module::factory()->create(['name' => 'Active Module']);
        $module2 = Module::factory()->create(['name' => 'Inactive Module']);
        $user = User::factory()->create();

        // Create active and inactive modules
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
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Query using active scope
        $activeModules = TenantModule::active()->get();

        // Assert only active modules are returned
        $this->assertCount(1, $activeModules);
        $this->assertEquals('Active Module', $activeModules->first()->module->name);
    }

    /** @test */
    public function it_has_inactive_scope()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $module1 = Module::factory()->create(['name' => 'Active Module']);
        $module2 = Module::factory()->create(['name' => 'Inactive Module']);
        $user = User::factory()->create();

        // Create active and inactive modules
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
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Query using inactive scope
        $inactiveModules = TenantModule::inactive()->get();

        // Assert only inactive modules are returned
        $this->assertCount(1, $inactiveModules);
        $this->assertEquals('Inactive Module', $inactiveModules->first()->module->name);
    }

    /** @test */
    public function it_can_be_activated()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $module = Module::factory()->create();
        $user = User::factory()->create();

        // Create inactive module
        $tenantModule = TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Activate the module
        $tenantModule->activate($user->id);

        // Assert it's now active
        $this->assertTrue($tenantModule->is_active);
        $this->assertEquals($user->id, $tenantModule->approved_by);
        $this->assertNotNull($tenantModule->approved_at);

        // Check database
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
            'approved_by' => $user->id,
        ]);
    }

    /** @test */
    public function it_can_be_deactivated()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $module = Module::factory()->create();
        $user = User::factory()->create();

        // Create active module
        $tenantModule = TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Deactivate the module
        $tenantModule->deactivate();

        // Assert it's now inactive
        $this->assertFalse($tenantModule->is_active);

        // Check database
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_can_check_if_tenant_has_active_module()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $activeModule = Module::factory()->create(['code' => 'ACTIVE_MOD']);
        $inactiveModule = Module::factory()->create(['code' => 'INACTIVE_MOD']);
        $user = User::factory()->create();

        // Create module relationships
        TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $activeModule->id,
            'is_active' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $inactiveModule->id,
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Check module status
        $this->assertTrue(TenantModule::isTenantModuleActive($tenant->id, $activeModule->id));
        $this->assertFalse(TenantModule::isTenantModuleActive($tenant->id, $inactiveModule->id));

        // Check by module code
        $this->assertTrue(TenantModule::isTenantModuleActiveByCode($tenant->id, 'ACTIVE_MOD'));
        $this->assertFalse(TenantModule::isTenantModuleActiveByCode($tenant->id, 'INACTIVE_MOD'));
    }

    /** @test */
    public function it_returns_active_modules_for_tenant()
    {
        // Create test data
        $tenant = Tenant::factory()->create();
        $module1 = Module::factory()->create(['name' => 'Module 1']);
        $module2 = Module::factory()->create(['name' => 'Module 2']);
        $module3 = Module::factory()->create(['name' => 'Module 3']);
        $user = User::factory()->create();

        // Activate two modules
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

        // Create inactive module
        TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module3->id,
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Get active modules
        $activeModules = TenantModule::getActiveModulesForTenant($tenant->id);

        // Assert result
        $this->assertCount(2, $activeModules);

        // Check module names
        $moduleNames = $activeModules->pluck('module.name')->toArray();
        $this->assertContains('Module 1', $moduleNames);
        $this->assertContains('Module 2', $moduleNames);
        $this->assertNotContains('Module 3', $moduleNames);
    }
}
