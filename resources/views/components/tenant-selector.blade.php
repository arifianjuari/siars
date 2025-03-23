@php
    $activeTenant = app(\App\Services\TenantSelectionService::class)->getActiveTenant();
    $user = auth()->user();
    $isSuperadmin = $user && $user->hasRole('Superadmin');
@endphp

@if($activeTenant)
    <div class="relative">
        <button id="tenant-dropdown-button" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none space-x-1">
            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-indigo-100 text-indigo-500 dark:bg-indigo-900 dark:text-indigo-300">
                {{ substr($activeTenant->name, 0, 1) }}
            </span>
            <span>{{ $activeTenant->name }}</span>
            @if($isSuperadmin)
                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            @endif
        </button>

        @if($isSuperadmin)
            <div id="tenant-dropdown-menu" class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden z-50">
                <div class="py-1" role="menu" aria-orientation="vertical">
                    <a href="http://localhost:8000/superadmin/select-tenant" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                        Ganti Rumah Sakit
                    </a>
                    <form action="{{ route('superadmin.impersonate-admin') }}" method="POST" class="block">
                        @csrf
                        <input type="hidden" name="tenant_id" value="{{ $activeTenant->id }}">
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                            Akses sebagai Admin RS
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@elseif($isSuperadmin)
    <a href="http://localhost:8000/superadmin/select-tenant" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-500 dark:bg-red-900 dark:text-red-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </span>
        <span class="ml-1">Pilih Rumah Sakit</span>
    </a>
@endif

<script>
    // Toggle dropdown when button is clicked
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.getElementById('tenant-dropdown-button');
        const dropdownMenu = document.getElementById('tenant-dropdown-menu');
        
        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', function() {
                dropdownMenu.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script> 