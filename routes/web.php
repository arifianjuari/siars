<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SnarsGroupController;
use App\Http\Controllers\SnarsChapterController;
use App\Http\Controllers\SnarsStandardController;
use App\Http\Controllers\SnarsAssessmentElementController;
use App\Http\Controllers\SnarsDocumentTypeController;
use App\Http\Controllers\SnarsRequiredDocumentController;
use App\Http\Controllers\SnarsDocumentController;
use App\Http\Controllers\SnarsAssessmentPeriodController;
use App\Http\Controllers\SnarsAssessmentController;
use App\Http\Controllers\SnarsAssessmentScoreController;
use App\Http\Controllers\SnarsFindingController;
use App\Http\Controllers\SnarsMonitoringScheduleController;
use App\Http\Controllers\SnarsMonitoringResultController;
use App\Http\Controllers\SnarsComplianceController;
use App\Http\Controllers\SnarsDashboardWidgetController;
use App\Http\Controllers\SnarsVersionController;
use App\Http\Controllers\SnarsUpdateController;
use App\Http\Controllers\SnarsNotificationController;
use App\Http\Controllers\SnarsSupportingDocumentController;
use App\Http\Controllers\SnarsSupportingDocumentCreateController;
use App\Http\Controllers\SnarsDashboardController;
use App\Http\Controllers\SnarsSurveyDateController;
use App\Http\Controllers\SuperadminModuleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\TenantModuleController;
use App\Http\Controllers\ModuleActivationRequestController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect dashboard to role-specific dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// SNARS Test Route
Route::get('/snars-test', function () {
    return view('snars-test');
})->middleware(['auth', 'verified'])->name('snars.test');

// SNARS Redirect Route
Route::get('/snars-redirect', function () {
    return view('snars-redirect');
})->middleware(['auth', 'verified'])->name('snars.redirect');

// Role-specific dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Superadmin dashboard
    Route::prefix('superadmin')->name('superadmin.')->middleware([CheckRole::class . ':Superadmin'])->group(function () {
        // Halaman pilih tenant dan dashboard
        Route::get('/select-tenant', [SuperadminController::class, 'selectTenant'])->name('select-tenant');
        Route::post('/set-tenant', [SuperadminController::class, 'setActiveTenant'])->name('set-tenant');
        Route::get('/dashboard', [SuperadminController::class, 'dashboard'])->name('dashboard');
        Route::post('/impersonate-admin', [SuperadminController::class, 'impersonateAdmin'])->name('impersonate-admin');
        Route::get('/exit-impersonation', [SuperadminController::class, 'exitImpersonation'])->name('exit-impersonation');

        // Manajemen tenant (rumah sakit)
        Route::get('/tenants/create', [SuperadminController::class, 'createTenant'])->name('tenants.create');
        Route::post('/tenants', [SuperadminController::class, 'storeTenant'])->name('tenants.store');

        // Pengelolaan modul (memerlukan tenant aktif)
        Route::middleware(['tenant.access'])->group(function () {
            Route::get('/modules', [SuperadminController::class, 'moduleList'])->name('modules');
            Route::get('/module-requests', [SuperadminController::class, 'moduleRequests'])->name('module-requests');
            Route::post('/modules/{moduleId}/toggle', [SuperadminController::class, 'toggleModuleStatus'])->name('modules.toggle-status');
            Route::post('/module-requests/{requestId}/process', [SuperadminController::class, 'processRequest'])->name('module-requests.process');
        });
    });

    // Manajemen Strategis dashboard
    Route::get('/dashboard/manajemen-strategis', [DashboardController::class, 'manajemenStrategisDashboard'])
        ->middleware([CheckRole::class . ':ManajemenStrategis'])
        ->name('dashboard.manajemen-strategis');

    // Manajemen Eksekutif dashboard
    Route::get('/dashboard/manajemen-eksekutif', [DashboardController::class, 'manajemenEksekutifDashboard'])
        ->middleware([CheckRole::class . ':ManajemenEksekutif'])
        ->name('dashboard.manajemen-eksekutif');

    // Manajemen Operasional dashboard
    Route::get('/dashboard/manajemen-operasional', [DashboardController::class, 'manajemenOperasionalDashboard'])
        ->middleware([CheckRole::class . ':ManajemenOperasional'])
        ->name('dashboard.manajemen-operasional');

    // Staf dashboard
    Route::get('/dashboard/staf', [DashboardController::class, 'stafDashboard'])
        ->middleware([CheckRole::class . ':Staf'])
        ->name('dashboard.staf');
});

// SNARS Module Routes
Route::middleware(['auth', 'verified', 'module.active:SNARS'])->prefix('snars')->name('snars.')->group(function () {
    // SnarsGroup routes
    Route::resource('groups', SnarsGroupController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);

    // SnarsGroup routes yang memerlukan role khusus (Superadmin atau ManajemenEksekutif)
    Route::middleware([CheckRole::class . ':Superadmin,ManajemenEksekutif'])->group(function () {
        Route::get('groups/create', [SnarsGroupController::class, 'create'])->name('groups.create');
        Route::post('groups', [SnarsGroupController::class, 'store'])->name('groups.store');
        Route::get('groups/{group}/edit', [SnarsGroupController::class, 'edit'])->name('groups.edit');
        Route::put('groups/{group}', [SnarsGroupController::class, 'update'])->name('groups.update');
        Route::delete('groups/{group}', [SnarsGroupController::class, 'destroy'])->name('groups.destroy');
        Route::post('groups/update-order', [SnarsGroupController::class, 'updateOrder'])->name('groups.update-order');
        Route::patch('groups/{id}/toggle-active', [SnarsGroupController::class, 'toggleActive'])->name('groups.toggle-active');
    });

    // SnarsChapter routes
    Route::resource('chapters', SnarsChapterController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);

    // SnarsChapter routes yang memerlukan role khusus (bukan Staf)
    Route::middleware([CheckRole::class . ':Superadmin,ManajemenEksekutif,ManajemenStrategis,ManajemenOperasional'])->group(function () {
        Route::get('chapters/create', [SnarsChapterController::class, 'create'])->name('chapters.create');
        Route::post('chapters', [SnarsChapterController::class, 'store'])->name('chapters.store');
        Route::get('chapters/{chapter}/edit', [SnarsChapterController::class, 'edit'])->name('chapters.edit');
        Route::put('chapters/{chapter}', [SnarsChapterController::class, 'update'])->name('chapters.update');
        Route::delete('chapters/{chapter}', [SnarsChapterController::class, 'destroy'])->name('chapters.destroy');
        Route::post('chapters/update-order', [SnarsChapterController::class, 'updateOrder'])->name('chapters.update-order');
        Route::patch('chapters/{id}/toggle-active', [SnarsChapterController::class, 'toggleActive'])->name('chapters.toggle-active');
    });

    // SnarsStandard routes
    Route::resource('standards', SnarsStandardController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);

    // SnarsStandard routes yang memerlukan role khusus (bukan Staf)
    Route::middleware([CheckRole::class . ':Superadmin,ManajemenEksekutif,ManajemenStrategis,ManajemenOperasional'])->group(function () {
        Route::get('standards/create', [SnarsStandardController::class, 'create'])->name('standards.create');
        Route::post('standards', [SnarsStandardController::class, 'store'])->name('standards.store');
        Route::get('standards/{standard}/edit', [SnarsStandardController::class, 'edit'])->name('standards.edit');
        Route::put('standards/{standard}', [SnarsStandardController::class, 'update'])->name('standards.update');
        Route::delete('standards/{standard}', [SnarsStandardController::class, 'destroy'])->name('standards.destroy');
        Route::post('standards/update-order', [SnarsStandardController::class, 'updateOrder'])->name('standards.update-order');
        Route::patch('standards/{id}/toggle-active', [SnarsStandardController::class, 'toggleActive'])->name('standards.toggle-active');
    });

    // SnarsAssessmentElement routes
    Route::resource('assessment-elements', SnarsAssessmentElementController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);

    // SnarsAssessmentElement routes yang memerlukan role khusus (bukan Staf)
    Route::middleware([CheckRole::class . ':Superadmin,ManajemenEksekutif,ManajemenStrategis,ManajemenOperasional'])->group(function () {
        Route::get('assessment-elements/create', [SnarsAssessmentElementController::class, 'create'])->name('assessment-elements.create');
        Route::post('assessment-elements', [SnarsAssessmentElementController::class, 'store'])->name('assessment-elements.store');
        Route::get('assessment-elements/{assessment_element}/edit', [SnarsAssessmentElementController::class, 'edit'])->name('assessment-elements.edit');
        Route::put('assessment-elements/{assessment_element}', [SnarsAssessmentElementController::class, 'update'])->name('assessment-elements.update');
        Route::delete('assessment-elements/{assessment_element}', [SnarsAssessmentElementController::class, 'destroy'])->name('assessment-elements.destroy');
        Route::post('assessment-elements/update-order', [SnarsAssessmentElementController::class, 'updateOrder'])->name('assessment-elements.update-order');
        Route::patch('assessment-elements/{id}/toggle-active', [SnarsAssessmentElementController::class, 'toggleActive'])->name('assessment-elements.toggle-active');
    });

    // SnarsDocumentType routes
    Route::resource('document-types', SnarsDocumentTypeController::class);
    Route::patch('document-types/{id}/toggle-active', [SnarsDocumentTypeController::class, 'toggleActive'])->name('document-types.toggle-active');

    // SnarsRequiredDocument routes
    Route::resource('required-documents', SnarsRequiredDocumentController::class);
    Route::patch('required-documents/{id}/toggle-mandatory', [SnarsRequiredDocumentController::class, 'toggleMandatory'])->name('required-documents.toggle-mandatory');

    // SnarsDocument routes
    Route::resource('documents', SnarsDocumentController::class);
    Route::post('documents/upload', [SnarsDocumentController::class, 'upload'])->name('documents.upload');
    Route::get('documents/{id}/download', [SnarsDocumentController::class, 'download'])->name('documents.download');
    Route::post('documents/{id}/version', [SnarsDocumentController::class, 'createVersion'])->name('documents.create-version');
    Route::get('documents/{id}/versions', [SnarsDocumentController::class, 'listVersions'])->name('documents.list-versions');

    // SnarsAssessmentPeriod routes
    Route::resource('assessment-periods', SnarsAssessmentPeriodController::class);
    Route::patch('assessment-periods/{id}/toggle-active', [SnarsAssessmentPeriodController::class, 'toggleActive'])->name('assessment-periods.toggle-active');

    // SnarsAssessment routes
    Route::resource('assessments', SnarsAssessmentController::class);
    Route::patch('assessments/{id}/change-status', [SnarsAssessmentController::class, 'changeStatus'])->name('assessments.change-status');

    // SnarsAssessmentScore routes
    Route::resource('assessment-scores', SnarsAssessmentScoreController::class);
    Route::post('assessment-scores/bulk-update', [SnarsAssessmentScoreController::class, 'bulkUpdate'])->name('assessment-scores.bulk-update');

    // SnarsFinding routes
    Route::resource('findings', SnarsFindingController::class);
    Route::patch('findings/{id}/change-status', [SnarsFindingController::class, 'changeStatus'])->name('findings.change-status');

    // SnarsMonitoringSchedule routes
    Route::resource('monitoring-schedules', SnarsMonitoringScheduleController::class);
    Route::patch('monitoring-schedules/{id}/change-status', [SnarsMonitoringScheduleController::class, 'changeStatus'])->name('monitoring-schedules.change-status');

    // SnarsMonitoringResult routes
    Route::resource('monitoring-results', SnarsMonitoringResultController::class);

    // SnarsCompliance routes
    Route::resource('compliance', SnarsComplianceController::class);
    Route::get('compliance/report', [SnarsComplianceController::class, 'report'])->name('compliance.report');
    Route::get('compliance/export', [SnarsComplianceController::class, 'export'])->name('compliance.export');

    // SnarsDashboardWidget routes
    Route::resource('dashboard-widgets', SnarsDashboardWidgetController::class);
    Route::post('dashboard-widgets/update-positions', [SnarsDashboardWidgetController::class, 'updatePositions'])->name('dashboard-widgets.update-positions');

    // SnarsVersion routes
    Route::resource('versions', SnarsVersionController::class);
    Route::patch('versions/{id}/toggle-active', [SnarsVersionController::class, 'toggleActive'])->name('versions.toggle-active');

    // SnarsUpdate routes
    Route::resource('updates', SnarsUpdateController::class);

    // SnarsNotification routes
    Route::resource('notifications', SnarsNotificationController::class);
    Route::patch('notifications/{id}/mark-as-read', [SnarsNotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::patch('notifications/{id}/mark-as-unread', [SnarsNotificationController::class, 'markAsUnread'])->name('notifications.mark-as-unread');
    Route::patch('notifications/mark-all-as-read', [SnarsNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // SnarsSupportingDocument routes
    Route::resource('supporting-documents', SnarsSupportingDocumentController::class);

    // Rute tambahan untuk pembuatan dokumen pendukung
    Route::get('create-document', [SnarsSupportingDocumentCreateController::class, 'index'])->name('supporting-documents.create-form');
    Route::post('store-document', [SnarsSupportingDocumentCreateController::class, 'store'])->name('supporting-documents.store-form');
    Route::get('supporting-documents/{id}/download', [SnarsSupportingDocumentController::class, 'download'])->name('supporting-documents.download');
    Route::get('supporting-documents/{id}/history', [SnarsSupportingDocumentController::class, 'history'])->name('supporting-documents.history');
    Route::get('supporting-documents/{id}/create-version', [SnarsSupportingDocumentController::class, 'createNewVersion'])->name('supporting-documents.create-version');
    Route::post('supporting-documents/{id}/store-version', [SnarsSupportingDocumentController::class, 'storeNewVersion'])->name('supporting-documents.store-version');
    Route::patch('supporting-documents/{id}/submit-for-review', [SnarsSupportingDocumentController::class, 'submitForReview'])->name('supporting-documents.submit-for-review');
    Route::patch('supporting-documents/{id}/approve', [SnarsSupportingDocumentController::class, 'approve'])->name('supporting-documents.approve');
    Route::patch('supporting-documents/{id}/reject', [SnarsSupportingDocumentController::class, 'reject'])->name('supporting-documents.reject');

    // SNARS Dashboard routes
    Route::get('dashboard', [SnarsDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/compliance-data', [SnarsDashboardController::class, 'getComplianceData'])->name('dashboard.compliance-data');
    Route::get('dashboard/upcoming-schedules', [SnarsDashboardController::class, 'getUpcomingSchedules'])->name('dashboard.upcoming-schedules');
    Route::get('dashboard/recent-findings', [SnarsDashboardController::class, 'getRecentFindings'])->name('dashboard.recent-findings');
    Route::get('dashboard/document-statistics', [SnarsDashboardController::class, 'getDocumentStatistics'])->name('dashboard.document-statistics');

    Route::put('/survey-date/update', [SnarsSurveyDateController::class, 'update'])
        ->name('survey-date.update')
        ->middleware(['auth', CheckRole::class . ':ManajemenEksekutif']);
});

Route::middleware(['auth', 'user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Route::get('/tenant/select', [TenantController::class, 'selectTenant'])
    //     ->name('tenant.select');
    Route::post('/tenant/set-active', [TenantController::class, 'setActiveTenant'])
        ->name('tenant.set-active');

    Route::middleware(['tenant.access'])->group(function () {
        // Rute modul umum yang dapat diakses oleh semua user
        // dengan tenant aktif
        // ...
    });

    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('/', function () {
                return view('superadmin.tenants.index', [
                    'tenants' => \App\Models\Tenant::orderBy('name')->get()
                ]);
            })->name('index');
            Route::get('/create', function () {
                return view('superadmin.tenants.create');
            })->name('create');
            Route::post('/', [TenantController::class, 'store'])->name('store');
            Route::get('/{tenant}/show', [TenantController::class, 'show'])->name('show');
            Route::get('/{tenant}/edit', [TenantController::class, 'edit'])->name('edit');
            Route::put('/{tenant}', [TenantController::class, 'update'])->name('update');
            Route::delete('/{tenant}', [TenantController::class, 'destroy'])->name('destroy');
            Route::delete('/{id}/force', [TenantController::class, 'forceDestroy'])->name('force-destroy');
            Route::post('/{id}/restore', [TenantController::class, 'restore'])->name('restore');
        });

        // User Management Routes
        Route::resource('users', \App\Http\Controllers\SuperadminUserController::class);
        Route::post('users/{id}/toggle-active', [\App\Http\Controllers\SuperadminUserController::class, 'toggleActive'])->name('users.toggle-active');

        // Tenant Admin Management Routes
        Route::prefix('tenant/{tenant}/admin')->name('tenant-admin.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperadminCreateTenantAdminController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\SuperadminCreateTenantAdminController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\SuperadminCreateTenantAdminController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [\App\Http\Controllers\SuperadminCreateTenantAdminController::class, 'edit'])->name('edit');
            Route::put('/{user}', [\App\Http\Controllers\SuperadminCreateTenantAdminController::class, 'update'])->name('update');
            Route::delete('/{user}', [\App\Http\Controllers\SuperadminCreateTenantAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-active', [\App\Http\Controllers\SuperadminCreateTenantAdminController::class, 'toggleActive'])->name('toggle-active');
        });

        Route::prefix('modules')->name('modules.')->group(function () {
            Route::get('/', [SuperadminModuleController::class, 'index'])->name('index');
            Route::get('/requests', [SuperadminModuleController::class, 'requests'])->name('requests');
            Route::post('/toggle/{moduleId}', [SuperadminModuleController::class, 'toggleStatus'])->name('toggle');
            Route::post('/process-request/{requestId}', [SuperadminModuleController::class, 'processRequest'])->name('process-request');
        });

        // Route untuk manajemen role_module_permissions
        Route::prefix('module-permissions')->name('module-permissions.')->group(function () {
            Route::get('/', [App\Http\Controllers\Superadmin\ModulePermissionController::class, 'index'])->name('index');
            Route::get('/{roleId}/{moduleId}/edit', [App\Http\Controllers\Superadmin\ModulePermissionController::class, 'edit'])->name('edit');
            Route::put('/{roleId}/{moduleId}', [App\Http\Controllers\Superadmin\ModulePermissionController::class, 'update'])->name('update');
            Route::post('/bulk-update', [App\Http\Controllers\Superadmin\ModulePermissionController::class, 'bulkUpdate'])->name('bulk-update');
        });
    });

    // Modul routes
    Route::resource('modules', ModuleController::class)->only(['index', 'show']);

    // Rute untuk modul di tenant tertentu
    Route::middleware(['tenant.access'])->group(function () {
        Route::get('/tenant/modules', [TenantModuleController::class, 'index'])
            ->name('tenant.modules.index');
    });

    // Rute untuk aktivasi/deaktivasi modul (superadmin)
    Route::middleware([CheckRole::class . ':Superadmin'])->group(function () {
        Route::post('/tenant/modules/{moduleId}/activate', [TenantModuleController::class, 'activate'])
            ->name('tenant.modules.activate');
        Route::post('/tenant/modules/{moduleId}/deactivate', [TenantModuleController::class, 'deactivate'])
            ->name('tenant.modules.deactivate');
    });

    // Rute untuk request aktivasi modul (admin RS)
    Route::post('/tenant/modules/request-activation', [TenantModuleController::class, 'requestActivation'])
        ->name('tenant.modules.request-activation');

    // Rute untuk ModuleActivationRequest
    Route::prefix('module-activation')->name('module-activation.')->group(function () {
        // Rute yang dapat diakses semua user
        Route::get('/', [ModuleActivationRequestController::class, 'index'])
            ->name('index');
        Route::get('/{id}', [ModuleActivationRequestController::class, 'show'])
            ->name('show');

        // Rute yang hanya dapat diakses pengguna dengan tenant
        Route::middleware(['tenant.access'])->group(function () {
            Route::get('/create', [ModuleActivationRequestController::class, 'create'])
                ->name('create');
            Route::post('/', [ModuleActivationRequestController::class, 'store'])
                ->name('store');
        });

        // Rute yang hanya dapat diakses superadmin
        Route::middleware([CheckRole::class . ':Superadmin'])->group(function () {
            Route::post('/{id}/process', [ModuleActivationRequestController::class, 'process'])
                ->name('process');
        });
    });

    // Rute untuk laporan modul
    Route::prefix('reports/modules')->name('reports.modules.')->group(function () {
        // Rute yang dapat diakses semua user
        Route::get('/', [App\Http\Controllers\ModuleReportController::class, 'index'])
            ->name('index');

        // Rute laporan histori aktivasi
        Route::get('/history', [App\Http\Controllers\ModuleReportController::class, 'activationHistory'])
            ->name('history');

        // Rute laporan modul aktif
        Route::get('/active', [App\Http\Controllers\ModuleReportController::class, 'activeModules'])
            ->name('active');

        // Rute ekspor
        Route::get('/export/active', [App\Http\Controllers\ModuleReportController::class, 'exportActiveModules'])
            ->name('export.active');
        Route::get('/export/history', [App\Http\Controllers\ModuleReportController::class, 'exportActivationHistory'])
            ->name('export.history');
    });
});

// TenantAdmin Routes
Route::middleware(['auth', 'role:TenantAdmin'])->prefix('tenantadmin')->name('tenantadmin.')->group(function () {
    // Dashboard Tenant Admin
    Route::get('/dashboard', [App\Http\Controllers\TenantAdminController::class, 'dashboard'])->name('dashboard');

    // User Management Routes
    Route::get('/users', [App\Http\Controllers\TenantAdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\TenantAdminController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\TenantAdminController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [App\Http\Controllers\TenantAdminController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [App\Http\Controllers\TenantAdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [App\Http\Controllers\TenantAdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\TenantAdminController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/toggle-active', [App\Http\Controllers\TenantAdminController::class, 'toggleActive'])->name('users.toggle-active');

    // Module Management Routes
    Route::get('/modules', [App\Http\Controllers\TenantAdminController::class, 'modules'])->name('modules');
    Route::post('/modules/request-activation', [App\Http\Controllers\TenantAdminController::class, 'requestModuleActivation'])->name('modules.request-activation');
});

// Modul SNARS
Route::prefix('snars')->middleware(['auth', 'verified', 'check.module.activation:SNARS'])->group(function () {
    Route::get('/dashboard', function () {
        return view('snars.dashboard');
    })->name('snars.dashboard');
});

// Modul Manajemen Risiko
Route::prefix('risk-management')->middleware(['auth', 'verified', \App\Http\Middleware\CheckModuleActiveMiddleware::class . ':RISK'])->group(function () {
    Route::get('/dashboard', function () {
        return view('risk-management.dashboard');
    })->name('risk-management.dashboard');
});

require __DIR__ . '/auth.php';
