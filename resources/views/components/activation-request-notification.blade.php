@php
use Illuminate\Support\Facades\Schema;

$user = auth()->user();
$pendingCount = 0;

// Hanya jalankan query jika tabel ada dan metode isSuperadmin tersedia
if (Schema::hasTable('module_activation_requests') && method_exists($user, 'isSuperadmin')) {
    if ($user->isSuperadmin()) {
        // Untuk superadmin: hitung semua permintaan yang belum diproses
        $pendingCount = \App\Models\ModuleActivationRequest::where('status', 'pending')->count();
    } elseif (isset($user->tenant_id) && $user->tenant_id) {
        // Untuk admin RS: hitung permintaan dari tenant mereka
        $pendingCount = \App\Models\ModuleActivationRequest::where('tenant_id', $user->tenant_id)
            ->where('status', 'pending')
            ->count();
    }
}
@endphp

@if($pendingCount > 0)
    <a href="{{ route('module-activation.index', ['status' => 'pending']) }}" class="relative inline-flex">
        <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md">
            <svg class="mr-1.5 h-4 w-4 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            Permintaan Aktivasi Modul
        </span>
        <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
            {{ $pendingCount }}
        </span>
    </a>
@endif 