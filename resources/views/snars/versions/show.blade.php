<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View SNARS Version') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('snars.versions.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Back to Versions
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium mb-4">Version Details</h3>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Version Number</p>
                                <p class="text-base">{{ $version->version_number }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Name</p>
                                <p class="text-base">{{ $version->name }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Description</p>
                                <p class="text-base">{{ $version->description }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Release Date</p>
                                <p class="text-base">{{ $version->release_date->format('d M Y') }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full {{ $version->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $version->is_active ? 'Active' : 'Inactive' }}
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Notes</p>
                                <p class="text-base">{{ $version->notes ?? 'No notes available' }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium mb-4">Updates</h3>
                            
                            @if($version->updates->count() > 0)
                                <div class="space-y-4">
                                    @foreach($version->updates as $update)
                                        <div class="border rounded p-4">
                                            <h4 class="font-medium">{{ $update->title }}</h4>
                                            <p class="text-sm text-gray-500">{{ $update->created_at->format('d M Y H:i') }}</p>
                                            <p class="mt-2">{{ $update->description }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No updates available for this version.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('snars.versions.edit', $version->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            Edit Version
                        </a>
                        
                        <form action="{{ route('snars.versions.toggle-active', $version->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-{{ $version->is_active ? 'red' : 'green' }}-600 text-white rounded hover:bg-{{ $version->is_active ? 'red' : 'green' }}-700">
                                {{ $version->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
