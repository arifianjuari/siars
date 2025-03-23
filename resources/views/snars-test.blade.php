<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('SNARS Test Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Pengujian Link SNARS</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-md font-medium">Link Langsung:</h4>
                            <a href="/snars/supporting-documents" class="text-blue-600 hover:underline">
                                /snars/supporting-documents (Link Langsung)
                            </a>
                        </div>
                        
                        <div>
                            <h4 class="text-md font-medium">Link dengan Route Helper:</h4>
                            <a href="{{ route('snars.supporting-documents.index') }}" class="text-blue-600 hover:underline">
                                route('snars.supporting-documents.index')
                            </a>
                        </div>
                        
                        <div class="mt-8">
                            <h4 class="text-md font-medium">Daftar Route yang Tersedia:</h4>
                            <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md mt-2 overflow-auto text-sm">
@php
    $routes = Route::getRoutes();
    $snarsRoutes = [];
    
    foreach ($routes as $route) {
        if (str_contains($route->getName() ?? '', 'snars.')) {
            $snarsRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
            ];
        }
    }
@endphp

@foreach ($snarsRoutes as $route)
{{ $route['method'] }} - {{ $route['uri'] }} - {{ $route['name'] }}
@endforeach
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
