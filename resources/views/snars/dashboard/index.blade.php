<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ editSurveyDate: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - SNARS Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .progress-bar {
            height: 10px;
            border-radius: 5px;
            background-color: #e5e7eb;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 5px;
        }
        .progress-bar-fill.green {
            background-color: #10b981;
        }
        .progress-bar-fill.orange {
            background-color: #f59e0b;
        }
        .progress-bar-fill.red {
            background-color: #ef4444;
        }
        
        /* Alpine.js utility untuk menyembunyikan elemen sebelum Alpine.js dimuat */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            <h1 class="ml-2 text-xl font-semibold text-gray-900">SIARS - SNARS Dashboard</h1>
                        </a>
                    </div>
                    <div class="flex items-center">
                        <!-- Notification Icon -->
                        <div class="relative mr-4">
                            <a href="{{ route('snars.notifications.index') }}" class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">3</span>
                            </a>
                        </div>
                        
                        <!-- User Profile Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <div>
                                <button @click="open = !open" type="button" class="flex items-center focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::check() ? Auth::user()->name : 'Guest') }}&background=0D8ABC&color=fff" alt="User profile">
                                    <div class="ml-2 flex flex-col items-start">
                                        <span class="text-sm font-medium text-gray-700">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</span>
                                        <span class="text-xs text-gray-500">{{ Auth::check() ? Auth::user()->getRoleNames()->first() : 'Guest' }}</span>
                                    </div>
                                    <svg class="ml-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div 
                                x-show="open" 
                                @click.away="open = false" 
                                x-transition:enter="transition ease-out duration-100" 
                                x-transition:enter-start="transform opacity-0 scale-95" 
                                x-transition:enter-end="transform opacity-100 scale-100" 
                                x-transition:leave="transition ease-in duration-75" 
                                x-transition:leave-start="transform opacity-100 scale-100" 
                                x-transition:leave-end="transform opacity-0 scale-95" 
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50" 
                                role="menu" 
                                aria-orientation="vertical" 
                                aria-labelledby="user-menu-button" 
                                tabindex="-1"
                                style="display: none;"
                            >
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Log Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Notifications -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
            @endif
            
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
            @endif
            
            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </span>
            </div>
            @endif

            <!-- Filters -->
            <div class="flex flex-wrap gap-4 mb-8">
                <div class="flex-1 min-w-[300px]">
                    <input type="text" placeholder="Cari standar, elemen penilaian, atau dokumen..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Semua Kelompok Standar</option>
                        @foreach($groups as $group)
                        <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Semua PIC</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Status Pemenuhan</option>
                        <option>Terpenuhi</option>
                        <option>Belum Terpenuhi</option>
                        <option>Dalam Proses</option>
                    </select>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Progress Total -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Progress Total</h2>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ $progressPercentage }}%</div>
                    <div class="progress-bar mb-2">
                        <div class="progress-bar-fill {{ $progressPercentage >= 70 ? 'green' : ($progressPercentage >= 50 ? 'orange' : 'red') }}" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>

                <!-- Survey Akreditasi -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Survey Akreditasi</h2>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-orange-500 mb-2">{{ $daysUntilSurvey }}</div>
                    <div class="text-sm text-gray-600 mb-2">Hari menuju survey ({{ $surveyDate->format('j M Y') }})</div>
                    
                    @if(Auth::check() && Auth::user()->hasRole('ManajemenEksekutif'))
                    <button 
                        @click="editSurveyDate = true" 
                        class="text-xs text-blue-600 hover:text-blue-800 flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit tanggal
                    </button>
                    @endif
                </div>

                <!-- Perlu Perhatian -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Perlu Perhatian</h2>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-red-500 mb-2">{{ $standardsNeedingAttention }}</div>
                    <div class="text-sm text-gray-600">Standar belum terpenuhi</div>
                </div>

                <!-- Dokumen -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Dokumen</h2>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ $documentCount }}</div>
                    <div class="text-sm text-gray-600">Dokumen terunggah</div>
                </div>
            </div>

            <!-- Timeline Progress -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-lg font-medium text-gray-900">Timeline Progres</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Jan 2025 -->
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="text-sm font-medium text-gray-600 mb-1">Jan {{ date('Y') }}</div>
                        <div class="text-2xl font-bold text-green-600 mb-2">{{ $monthlyProgress['jan']['percentage'] }}%</div>
                        <div class="text-xs text-green-700 mb-2">{{ $monthlyProgress['jan']['elements'] }} elemen terpenuhi</div>
                    </div>
                    
                    <!-- Feb 2025 -->
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <div class="text-sm font-medium text-gray-600 mb-1">Feb {{ date('Y') }}</div>
                        <div class="text-2xl font-bold text-yellow-600 mb-2">{{ $monthlyProgress['feb']['percentage'] }}%</div>
                        <div class="text-xs text-yellow-700 mb-2">{{ $monthlyProgress['feb']['elements'] }} elemen terpenuhi</div>
                    </div>
                    
                    <!-- Mar 2025 -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="text-sm font-medium text-gray-600 mb-1">Mar {{ date('Y') }}</div>
                        <div class="text-2xl font-bold text-gray-600 mb-2">-</div>
                        <div class="text-xs text-gray-700 mb-2">Target: {{ $monthlyProgress['mar']['target'] }}%</div>
                    </div>
                </div>
            </div>

            <!-- Kelompok Standar SNARS -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Kelompok Standar SNARS</h2>
                    <a href="{{ route('snars.groups.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua Kelompok</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($groups as $group)
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-{{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $group['progress'] >= 70 ? 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' : ($group['progress'] >= 50 ? 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' : 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4') }}" />
                            </svg>
                            <h3 class="text-md font-medium text-gray-900">{{ $group['name'] }}</h3>
                        </div>
                        <div class="mb-1 flex justify-between">
                            <span class="text-sm text-gray-600">Progress</span>
                            <span class="text-sm font-medium text-{{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}-600">{{ $group['progress'] }}%</span>
                        </div>
                        <div class="progress-bar mb-3">
                            <div class="progress-bar-fill {{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}" style="width: {{ $group['progress'] }}%"></div>
                        </div>
                        <div class="text-sm text-gray-600 mb-4">{{ $group['fulfilled_elements'] }}/{{ $group['total_elements'] }} Elemen terpenuhi</div>
                        <div class="text-xs text-gray-500 mb-4">PIC: {{ $group['pic'] }}</div>
                        <div class="flex justify-between">
                            <a href="{{ route('snars.assessment-elements.index', ['group_id' => $group['id']]) }}" class="inline-flex items-center px-3 py-1 bg-{{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}-100 border border-transparent rounded-md font-semibold text-xs text-{{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}-800 uppercase tracking-widest hover:bg-{{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}-200 focus:outline-none focus:bg-{{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}-200 active:bg-{{ $group['progress'] >= 70 ? 'green' : ($group['progress'] >= 50 ? 'orange' : 'red') }}-300 transition ease-in-out duration-150 mr-2">{{ __('Lihat Detail') }}</a>
                            <a href="{{ route('snars.supporting-documents.create-form') }}" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-white border-t border-gray-200 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">© {{ date('Y') }} SIARS - Sistem Informasi Akreditasi Rumah Sakit</p>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit Tanggal Survey -->
    <div 
        x-show="editSurveyDate" 
        class="fixed inset-0 overflow-y-auto z-50" 
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="editSurveyDate" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="fixed inset-0 transition-opacity" 
                aria-hidden="true"
                @click="editSurveyDate = false"
            >
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                x-show="editSurveyDate" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" 
                role="dialog" 
                aria-modal="true" 
                aria-labelledby="modal-headline"
                @click.away="editSurveyDate = false"
            >
                <form action="{{ route('snars.survey-date.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                    Ubah Tanggal Survey Akreditasi
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Pilih tanggal baru untuk pelaksanaan survey akreditasi.
                                    </p>
                                    <div class="mb-4">
                                        <label for="survey_date" class="block text-sm font-medium text-gray-700">Tanggal Survey</label>
                                        <input 
                                            type="date" 
                                            name="survey_date" 
                                            id="survey_date" 
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            value="{{ $surveyDate->format('Y-m-d') }}"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="editSurveyDate = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
