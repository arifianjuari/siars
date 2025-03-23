<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if(request()->is('risk-management*'))
                    <!-- Horizontal Menu for Risk Management -->
                    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="container-fluid">
                            <div class="d-flex flex-column flex-md-row">
                                <div class="p-3 border-bottom border-md-bottom-0 border-md-end">
                                    <h4 class="text-gray-800 dark:text-gray-200 fs-5 mb-md-0">Manajemen Risiko</h4>
                                </div>
                                
                                <div class="p-2 d-flex flex-wrap">
                                    <a href="{{ route('risk-management.dashboard') }}" class="btn btn-sm {{ request()->routeIs('risk-management.dashboard') ? 'btn-primary' : 'btn-outline-secondary' }} m-1">
                                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                    </a>
                                    
                                    <div class="dropdown m-1">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog me-1"></i> Pengaturan
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                                            <li>
                                                <a href="{{ route('risk-management.settings.locations') }}" class="dropdown-item {{ request()->routeIs('risk-management.settings.locations') ? 'active' : '' }}">
                                                    <i class="fas fa-map-marker-alt me-1"></i> Lokasi
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.settings.incident-types') }}" class="dropdown-item {{ request()->routeIs('risk-management.settings.incident-types') ? 'active' : '' }}">
                                                    <i class="fas fa-list-alt me-1"></i> Jenis Insiden
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.settings.risk-matrix') }}" class="dropdown-item {{ request()->routeIs('risk-management.settings.risk-matrix') ? 'active' : '' }}">
                                                    <i class="fas fa-table me-1"></i> Matriks Risiko
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a href="{{ route('risk-management.categories.index') }}" class="dropdown-item {{ request()->routeIs('risk-management.categories.*') ? 'active' : '' }}">
                                                    <i class="fas fa-tags me-1"></i> Kategori Risiko
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.factors.index') }}" class="dropdown-item {{ request()->routeIs('risk-management.factors.*') ? 'active' : '' }}">
                                                    <i class="fas fa-diagnoses me-1"></i> Faktor Penyebab
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="dropdown m-1">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="incidentsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Insiden
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="incidentsDropdown">
                                            <li>
                                                <a href="{{ route('risk-management.incidents.index') }}" class="dropdown-item {{ request()->routeIs('risk-management.incidents.index') ? 'active' : '' }}">
                                                    <i class="fas fa-list me-1"></i> Daftar Insiden
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.incidents.create') }}" class="dropdown-item {{ request()->routeIs('risk-management.incidents.create') ? 'active' : '' }}">
                                                    <i class="fas fa-plus-circle me-1"></i> Tambah Insiden
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="dropdown m-1">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="analysisDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-search me-1"></i> Analisis
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="analysisDropdown">
                                            <li>
                                                <a href="{{ route('risk-management.analysis.index') }}" class="dropdown-item {{ request()->routeIs('risk-management.analysis.index') ? 'active' : '' }}">
                                                    <i class="fas fa-file-alt me-1"></i> Daftar Analisis
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.analysis.create') }}" class="dropdown-item {{ request()->routeIs('risk-management.analysis.create') ? 'active' : '' }}">
                                                    <i class="fas fa-microscope me-1"></i> Analisis Baru
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="dropdown m-1">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="monitoringDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-clipboard-check me-1"></i> Monitoring
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="monitoringDropdown">
                                            <li>
                                                <a href="{{ route('risk-management.monitoring.index') }}" class="dropdown-item {{ request()->routeIs('risk-management.monitoring.index') ? 'active' : '' }}">
                                                    <i class="fas fa-tasks me-1"></i> Status Insiden
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.mitigations.index') }}" class="dropdown-item {{ request()->routeIs('risk-management.mitigations.*') ? 'active' : '' }}">
                                                    <i class="fas fa-shield-alt me-1"></i> Mitigasi Risiko
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="dropdown m-1">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="reportsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-chart-pie me-1"></i> Laporan
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                                            <li>
                                                <a href="{{ route('risk-management.reports.show', 'matrix') }}" class="dropdown-item {{ request()->route('type') === 'matrix' ? 'active' : '' }}">
                                                    <i class="fas fa-border-all me-1"></i> Matriks Risiko
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.reports.show', 'category') }}" class="dropdown-item {{ request()->route('type') === 'category' ? 'active' : '' }}">
                                                    <i class="fas fa-folder me-1"></i> Risiko per Kategori
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('risk-management.reports.show', 'trend') }}" class="dropdown-item {{ request()->route('type') === 'trend' ? 'active' : '' }}">
                                                    <i class="fas fa-chart-line me-1"></i> Tren Risiko
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a href="{{ route('risk-management.reports.index') }}" class="dropdown-item {{ request()->routeIs('risk-management.reports.index') ? 'active' : '' }}">
                                                    <i class="fas fa-file-invoice me-1"></i> Semua Laporan
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Content without Sidebar -->
                    <div class="w-full p-4">
                        @yield('content')
                    </div>
                @else
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot }}
                    @endif
                @endif
            </main>
        </div>
        
        <!-- Bootstrap JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        
        @stack('scripts')
    </body>
</html>
