<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ModuleActivationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleActivationRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_pending_status_when_created()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();

        // Create request
        $request = ModuleActivationRequest::create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
            'notes' => 'Test request',
            'requested_at' => now(),
        ]);

        // Assert it has pending status
        $this->assertEquals('pending', $request->status);
    }

    /** @test */
    public function it_can_be_approved()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $requestedBy = User::factory()->create();
        $approvedBy = User::factory()->create();

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $requestedBy->id,
            'status' => 'pending',
            'notes' => 'Test request',
            'requested_at' => now(),
        ]);

        // Approve request
        $request->approve($approvedBy->id, 'Approved for testing');

        // Assert status changes
        $this->assertEquals('approved', $request->status);
        $this->assertEquals($approvedBy->id, $request->approved_by);
        $this->assertNotNull($request->approved_at);
        $this->assertEquals('Approved for testing', $request->approval_notes);
    }

    /** @test */
    public function it_can_be_rejected()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $requestedBy = User::factory()->create();
        $rejectedBy = User::factory()->create();

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $requestedBy->id,
            'status' => 'pending',
            'notes' => 'Test request',
            'requested_at' => now(),
        ]);

        // Reject request
        $request->reject($rejectedBy->id, 'Rejected for testing');

        // Assert status changes
        $this->assertEquals('rejected', $request->status);
        $this->assertEquals($rejectedBy->id, $request->rejected_by);
        $this->assertNotNull($request->rejected_at);
        $this->assertEquals('Rejected for testing', $request->rejection_notes);
    }

    /** @test */
    public function it_throws_exception_when_approving_non_pending_request()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $requestedBy = User::factory()->create();
        $approvedBy = User::factory()->create();

        // Create request that is already rejected
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $requestedBy->id,
            'status' => 'rejected',
            'notes' => 'Test request',
            'requested_at' => now(),
            'rejected_by' => $approvedBy->id,
            'rejected_at' => now(),
        ]);

        // Set up expectation for exception
        $this->expectException(\InvalidArgumentException::class);

        // Try to approve already rejected request
        $request->approve($approvedBy->id, 'Should fail');
    }

    /** @test */
    public function it_throws_exception_when_rejecting_non_pending_request()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $requestedBy = User::factory()->create();
        $approvedBy = User::factory()->create();

        // Create request that is already approved
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $requestedBy->id,
            'status' => 'approved',
            'notes' => 'Test request',
            'requested_at' => now(),
            'approved_by' => $approvedBy->id,
            'approved_at' => now(),
        ]);

        // Set up expectation for exception
        $this->expectException(\InvalidArgumentException::class);

        // Try to reject already approved request
        $request->reject($approvedBy->id, 'Should fail');
    }

    /** @test */
    public function it_belongs_to_module()
    {
        // Create test data
        $module = Module::factory()->create(['name' => 'Test Module']);
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
        ]);

        // Assert relationship
        $this->assertInstanceOf(Module::class, $request->module);
        $this->assertEquals($module->id, $request->module->id);
        $this->assertEquals('Test Module', $request->module->name);
    }

    /** @test */
    public function it_belongs_to_tenant()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
        $user = User::factory()->create();

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
        ]);

        // Assert relationship
        $this->assertInstanceOf(Tenant::class, $request->tenant);
        $this->assertEquals($tenant->id, $request->tenant->id);
        $this->assertEquals('Test Tenant', $request->tenant->name);
    }

    /** @test */
    public function it_belongs_to_requester()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['name' => 'Requester User']);

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
        ]);

        // Assert relationship
        $this->assertInstanceOf(User::class, $request->requester);
        $this->assertEquals($user->id, $request->requester->id);
        $this->assertEquals('Requester User', $request->requester->name);
    }

    /** @test */
    public function it_belongs_to_approver()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $requester = User::factory()->create();
        $approver = User::factory()->create(['name' => 'Approver User']);

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $requester->id,
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        // Assert relationship
        $this->assertInstanceOf(User::class, $request->approver);
        $this->assertEquals($approver->id, $request->approver->id);
        $this->assertEquals('Approver User', $request->approver->name);
    }

    /** @test */
    public function it_belongs_to_rejector()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $requester = User::factory()->create();
        $rejector = User::factory()->create(['name' => 'Rejector User']);

        // Create request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $requester->id,
            'status' => 'rejected',
            'rejected_by' => $rejector->id,
            'rejected_at' => now(),
        ]);

        // Assert relationship
        $this->assertInstanceOf(User::class, $request->rejector);
        $this->assertEquals($rejector->id, $request->rejector->id);
        $this->assertEquals('Rejector User', $request->rejector->name);
    }

    /** @test */
    public function it_can_scope_to_pending_requests()
    {
        // Create test data
        $module = Module::factory()->create();
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();

        // Create requests with different statuses
        ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
            'status' => 'pending',
        ]);

        ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
            'status' => 'approved',
        ]);

        ModuleActivationRequest::factory()->create([
            'module_id' => $module->id,
            'tenant_id' => $tenant->id,
            'requested_by' => $user->id,
            'status' => 'rejected',
        ]);

        // Query using scope
        $pendingRequests = ModuleActivationRequest::pending()->get();

        // Assert result
        $this->assertCount(1, $pendingRequests);
        $this->assertEquals('pending', $pendingRequests->first()->status);
    }
}
