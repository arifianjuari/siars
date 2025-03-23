<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantModule;
use App\Models\ModuleActivationRequest;
use App\Models\ModuleActivationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ModuleActivationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $tenant;
    protected $module;

    public function setUp(): void
    {
        parent::setUp();

        // Create test users with different roles
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $this->user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);

        // Assign roles (this would depend on your actual role implementation)
        // For this test, we'll assume there's a method to assign an admin role
        $this->admin->assignRole('admin');

        // Create test tenant and module
        $this->tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'code' => 'TEST',
        ]);

        $this->module = Module::factory()->create([
            'name' => 'Test Module',
            'code' => 'TEST_MOD',
            'description' => 'A test module for feature testing',
        ]);
    }

    /** @test */
    public function admin_can_activate_module_directly()
    {
        // Login as admin
        $this->actingAs($this->admin);

        // Submit activation request
        $response = $this->post(route('modules.activate'), [
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'notes' => 'Aktivasi langsung oleh admin',
        ]);

        // Assert redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check database for module activation
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
            'approved_by' => $this->admin->id,
        ]);

        // Check audit log
        $this->assertDatabaseHas('module_activation_logs', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'user_id' => $this->admin->id,
            'action' => 'activated',
        ]);
    }

    /** @test */
    public function regular_user_can_request_module_activation()
    {
        // Login as regular user
        $this->actingAs($this->user);

        // Submit activation request
        $response = $this->post(route('modules.request'), [
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'notes' => 'Permintaan aktivasi oleh pengguna',
        ]);

        // Assert redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check database for module activation request
        $this->assertDatabaseHas('module_activation_requests', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'requested_by' => $this->user->id,
            'status' => 'pending',
            'notes' => 'Permintaan aktivasi oleh pengguna',
        ]);

        // Check audit log
        $this->assertDatabaseHas('module_activation_logs', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'user_id' => $this->user->id,
            'action' => 'requested',
        ]);

        // Module should not be activated yet
        $this->assertDatabaseMissing('tenant_modules', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_approve_activation_request()
    {
        // Create a pending request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->user->id,
            'status' => 'pending',
            'notes' => 'Permintaan aktivasi untuk disetujui',
            'requested_at' => now(),
        ]);

        // Login as admin
        $this->actingAs($this->admin);

        // Approve the request
        $response = $this->post(route('modules.approve', $request->id), [
            'notes' => 'Disetujui untuk testing',
        ]);

        // Assert redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check request is updated
        $this->assertDatabaseHas('module_activation_requests', [
            'id' => $request->id,
            'status' => 'approved',
            'approved_by' => $this->admin->id,
        ]);

        // Check module is activated
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
        ]);

        // Check audit logs
        $this->assertDatabaseHas('module_activation_logs', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'user_id' => $this->admin->id,
            'action' => 'approved',
            'request_id' => $request->id,
        ]);

        $this->assertDatabaseHas('module_activation_logs', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'user_id' => $this->admin->id,
            'action' => 'activated',
        ]);
    }

    /** @test */
    public function admin_can_reject_activation_request()
    {
        // Create a pending request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->user->id,
            'status' => 'pending',
            'notes' => 'Permintaan aktivasi untuk ditolak',
            'requested_at' => now(),
        ]);

        // Login as admin
        $this->actingAs($this->admin);

        // Reject the request
        $response = $this->post(route('modules.reject', $request->id), [
            'notes' => 'Ditolak untuk testing',
        ]);

        // Assert redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check request is updated
        $this->assertDatabaseHas('module_activation_requests', [
            'id' => $request->id,
            'status' => 'rejected',
            'rejected_by' => $this->admin->id,
            'rejection_notes' => 'Ditolak untuk testing',
        ]);

        // Module should not be activated
        $this->assertDatabaseMissing('tenant_modules', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
        ]);

        // Check audit log
        $this->assertDatabaseHas('module_activation_logs', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'user_id' => $this->admin->id,
            'action' => 'rejected',
            'request_id' => $request->id,
        ]);
    }

    /** @test */
    public function admin_can_deactivate_module()
    {
        // Activate module first
        TenantModule::factory()->create([
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        // Login as admin
        $this->actingAs($this->admin);

        // Deactivate the module
        $response = $this->post(route('modules.deactivate'), [
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'notes' => 'Deaktivasi untuk testing',
        ]);

        // Assert redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check module is deactivated
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => false,
        ]);

        // Check audit log
        $this->assertDatabaseHas('module_activation_logs', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'user_id' => $this->admin->id,
            'action' => 'deactivated',
        ]);
    }

    /** @test */
    public function regular_user_cannot_activate_module_directly()
    {
        // Login as regular user
        $this->actingAs($this->user);

        // Try to activate directly
        $response = $this->post(route('modules.activate'), [
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'notes' => 'Percobaan aktivasi langsung',
        ]);

        // Assert forbidden status
        $response->assertForbidden();

        // Module should not be activated
        $this->assertDatabaseMissing('tenant_modules', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function regular_user_cannot_approve_requests()
    {
        // Create a pending request
        $request = ModuleActivationRequest::factory()->create([
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->user->id,
            'status' => 'pending',
            'notes' => 'Permintaan aktivasi',
            'requested_at' => now(),
        ]);

        // Login as regular user
        $this->actingAs($this->user);

        // Try to approve the request
        $response = $this->post(route('modules.approve', $request->id), [
            'notes' => 'Percobaan persetujuan',
        ]);

        // Assert forbidden status
        $response->assertForbidden();

        // Request status should remain pending
        $this->assertDatabaseHas('module_activation_requests', [
            'id' => $request->id,
            'status' => 'pending',
        ]);

        // Module should not be activated
        $this->assertDatabaseMissing('tenant_modules', [
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function module_access_is_denied_when_not_activated()
    {
        // Login as regular user
        $this->actingAs($this->user);

        // Try to access a module-specific route (route depends on your implementation)
        $response = $this->get(route('modules.access', [
            'moduleCode' => $this->module->code,
            'tenantCode' => $this->tenant->code,
        ]));

        // Assert forbidden or redirect to activation request page
        $response->assertStatus(403); // or check for specific redirect
    }

    /** @test */
    public function module_access_is_allowed_when_activated()
    {
        // Activate module first
        TenantModule::factory()->create([
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'is_active' => true,
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        // Login as regular user
        $this->actingAs($this->user);

        // Try to access a module-specific route
        $response = $this->get(route('modules.access', [
            'moduleCode' => $this->module->code,
            'tenantCode' => $this->tenant->code,
        ]));

        // Assert successful access
        $response->assertSuccessful();
    }

    /** @test */
    public function can_view_module_activation_reports()
    {
        // Create some module activation logs
        ModuleActivationLog::factory()->count(5)->create([
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->admin->id,
            'action' => 'activated',
            'action_at' => now(),
        ]);

        // Login as admin
        $this->actingAs($this->admin);

        // Access the reports page
        $response = $this->get(route('reports.modules'));

        // Assert successful access and content
        $response->assertSuccessful();
        $response->assertSee('Laporan Aktivasi Modul');
        $response->assertSee($this->module->name);
        $response->assertSee($this->tenant->name);
    }

    /** @test */
    public function can_export_module_activation_report()
    {
        // Create some module activation logs
        ModuleActivationLog::factory()->count(5)->create([
            'module_id' => $this->module->id,
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->admin->id,
            'action' => 'activated',
            'action_at' => now(),
        ]);

        // Login as admin
        $this->actingAs($this->admin);

        // Request a CSV export
        $response = $this->get(route('reports.modules.export', ['format' => 'csv']));

        // Assert successful download response
        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=module_activation_report.csv');
    }
}
