<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantModule;
use App\Models\ModuleActivationRequest;
use App\Services\ModuleActivationService;
use App\Services\ModuleService;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Access\AuthorizationException;

class ModuleActivationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleActivationService $moduleActivationService;
    protected ModuleService $moduleService;
    protected AuditService $auditService;

    public function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->moduleService = Mockery::mock(ModuleService::class);
        $this->auditService = Mockery::mock(AuditService::class);

        // Create service instance with mocked dependencies
        $this->moduleActivationService = new ModuleActivationService(
            $this->moduleService,
            $this->auditService
        );

        // Disable event dispatching during tests
        Event::fake();
    }

    /** @test */
    public function it_activates_module_for_tenant()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'Superadmin']);
        $module = Module::factory()->create(['name' => 'Test Module']);
        $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);

        // Configure mock
        $this->auditService->shouldReceive('logModuleActivated')
            ->once()
            ->andReturn(true);

        // User can activate module
        $this->moduleService->shouldReceive('isModuleActiveForTenant')
            ->once()
            ->andReturn(false);

        // Call the method
        $result = $this->moduleActivationService->activateModule(
            $module->id,
            $tenant->id,
            $user->id
        );

        // Assert result
        $this->assertInstanceOf(TenantModule::class, $result);
        $this->assertTrue($result->is_active);
        $this->assertEquals($user->id, $result->approved_by);

        // Verify data in database
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
            'approved_by' => $user->id,
        ]);
    }

    /** @test */
    public function it_deactivates_module_for_tenant()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'Superadmin']);
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Create tenant module relationship
        $tenantModule = TenantModule::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
            'approved_by' => $user->id,
        ]);

        // Configure mock
        $this->auditService->shouldReceive('logModuleDeactivated')
            ->once()
            ->andReturn(true);

        // Call the method
        $result = $this->moduleActivationService->deactivateModule(
            $module->id,
            $tenant->id
        );

        // Assert result
        $this->assertTrue($result);

        // Verify data in database
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_creates_module_activation_request()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'TenantAdmin']);
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Configure mocks
        $this->moduleService->shouldReceive('isModuleActiveForTenant')
            ->once()
            ->with($module->id, $tenant->id)
            ->andReturn(false);

        $this->auditService->shouldReceive('logActivationRequest')
            ->once()
            ->andReturn(true);

        // Call the method
        $result = $this->moduleActivationService->requestActivation(
            $module->id,
            $tenant->id,
            $user->id,
            'Permintaan aktivasi modul untuk pengujian'
        );

        // Assert result
        $this->assertInstanceOf(ModuleActivationRequest::class, $result);
        $this->assertEquals('pending', $result->status);
        $this->assertEquals($user->id, $result->requested_by);

        // Verify data in database
        $this->assertDatabaseHas('module_activation_requests', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'requested_by' => $user->id,
            'status' => 'pending',
            'notes' => 'Permintaan aktivasi modul untuk pengujian',
        ]);
    }

    /** @test */
    public function it_prevents_creating_request_for_already_active_module()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'TenantAdmin']);
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Configure mock to return true (module already active)
        $this->moduleService->shouldReceive('isModuleActiveForTenant')
            ->once()
            ->with($module->id, $tenant->id)
            ->andReturn(true);

        // Expect exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Modul sudah aktif untuk tenant ini');

        // Call the method
        $this->moduleActivationService->requestActivation(
            $module->id,
            $tenant->id,
            $user->id,
            'Permintaan aktivasi modul'
        );
    }

    /** @test */
    public function it_approves_module_activation_request()
    {
        // Create test data
        $requester = User::factory()->create(['role' => 'TenantAdmin']);
        $processor = User::factory()->create(['role' => 'Superadmin']);
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Create pending request
        $request = ModuleActivationRequest::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'requested_by' => $requester->id,
            'status' => 'pending',
        ]);

        // Configure mocks
        $this->auditService->shouldReceive('logRequestProcessed')
            ->once()
            ->andReturn(true);

        // Call the method
        $result = $this->moduleActivationService->processActivationRequest(
            $request,
            $processor,
            'approved',
            'Permintaan disetujui'
        );

        // Assert result
        $this->assertInstanceOf(ModuleActivationRequest::class, $result);
        $this->assertEquals('approved', $result->status);
        $this->assertEquals($processor->id, $result->processed_by);

        // Verify data in database
        $this->assertDatabaseHas('module_activation_requests', [
            'id' => $request->id,
            'status' => 'approved',
            'processed_by' => $processor->id,
            'notes' => 'Permintaan disetujui',
        ]);

        // Verify module activation
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_rejects_module_activation_request()
    {
        // Create test data
        $requester = User::factory()->create(['role' => 'TenantAdmin']);
        $processor = User::factory()->create(['role' => 'Superadmin']);
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();

        // Create pending request
        $request = ModuleActivationRequest::factory()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'requested_by' => $requester->id,
            'status' => 'pending',
        ]);

        // Configure mocks
        $this->auditService->shouldReceive('logRequestProcessed')
            ->once()
            ->andReturn(true);

        // Call the method
        $result = $this->moduleActivationService->processActivationRequest(
            $request,
            $processor,
            'rejected',
            'Permintaan ditolak'
        );

        // Assert result
        $this->assertInstanceOf(ModuleActivationRequest::class, $result);
        $this->assertEquals('rejected', $result->status);
        $this->assertEquals($processor->id, $result->processed_by);

        // Verify data in database
        $this->assertDatabaseHas('module_activation_requests', [
            'id' => $request->id,
            'status' => 'rejected',
            'processed_by' => $processor->id,
            'notes' => 'Permintaan ditolak',
        ]);

        // Verify module is not activated
        $this->assertDatabaseMissing('tenant_modules', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
        ]);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
