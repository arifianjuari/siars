<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\SuperadminTenantAccess;
use App\Providers\RouteServiceProvider;
use App\Services\TenantSelectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Service untuk pemilihan tenant
     *
     * @var TenantSelectionService
     */
    protected $tenantSelectionService;

    /**
     * Konstruktor
     *
     * @param TenantSelectionService $tenantSelectionService
     */
    public function __construct(TenantSelectionService $tenantSelectionService = null)
    {
        $this->tenantSelectionService = $tenantSelectionService ?? app(TenantSelectionService::class);
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Coba autentikasi user
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Jika sukses login, cek status aktif
            $user = Auth::user();

            // Jika user tidak aktif, logout dan beri pesan error
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda belum diaktifkan oleh Superadmin. Silahkan hubungi administrator.']);
            }

            $request->session()->regenerate();

            // Logika untuk superadmin: cek perlu pilih tenant atau gunakan default
            if ($user->isSuperadmin()) {
                // Cek apakah ada default tenant
                $defaultAccess = SuperadminTenantAccess::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->first();

                if ($defaultAccess) {
                    // Set tenant default sebagai aktif
                    $this->tenantSelectionService->setActiveTenant($defaultAccess->tenant_id);
                    return redirect()->intended(RouteServiceProvider::HOME);
                } else {
                    // Redirect ke halaman pilih tenant
                    return redirect()->route('superadmin.select-tenant');
                }
            }

            // Jika bukan superadmin, tenant sudah terhubung langsung ke user
            if ($user->tenant_id) {
                $this->tenantSelectionService->setActiveTenant($user->tenant_id);
                return redirect()->intended(RouteServiceProvider::HOME);
            } else if ($user->isSuperadmin()) {
                // If user is superadmin, redirect to tenant selection
                return redirect()->route('superadmin.select-tenant');
            } else {
                // User tidak memiliki tenant
                return redirect()->intended(RouteServiceProvider::HOME);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan tidak valid.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Hapus tenant aktif dari sesi
        if ($this->tenantSelectionService) {
            $this->tenantSelectionService->clearActiveTenant();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
