@extends('layouts.app')

@section('title', 'Edit Camera')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Edit Camera</h2>
                    <p class="text-amber-100 text-sm mt-1">Update camera configuration</p>
                </div>
                <a href="{{ route('cctv.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Cameras
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <form method="POST" action="{{ route('cctv.update', $cctv) }}">
                @csrf
                @method('PUT')
                
                <div class="p-8 space-y-6">
                    <!-- Camera Status Badge -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Camera ID</p>
                                    <p class="font-mono font-semibold text-gray-800">#{{ str_pad($cctv->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 {{ $cctv->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full animate-pulse"></div>
                                <span class="text-sm {{ $cctv->is_active ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $cctv->is_active ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Camera Details -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Camera Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Camera Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="camera_name" value="{{ old('camera_name', $cctv->camera_name) }}" required
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                @error('camera_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Camera Location</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="camera_location" value="{{ old('camera_location', $cctv->camera_location) }}"
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">IP Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9h3m-3 0H3m9 9a9 9 0 01-9-9m9 9c1.66 0 3-4 3-9s-1.34-9-3-9m0 18c-1.66 0-3-4-3-9s1.34-9 3-9"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="camera_ip" value="{{ old('camera_ip', $cctv->camera_ip) }}"
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200"
                                           placeholder="192.168.1.100">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Camera IP address on your network</p>
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stream URL</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="stream_url" value="{{ old('stream_url', $cctv->stream_url) }}"
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200"
                                           placeholder="rtsp://192.168.1.100:554/stream">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">RTSP or HTTP stream URL</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Camera Settings -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Camera Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $cctv->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-5 h-5">
                                <span class="ml-2 text-sm text-gray-700">Active Camera</span>
                            </label>
                            
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="recording_enabled" value="1" {{ old('recording_enabled', $cctv->recording_enabled) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-5 h-5">
                                <span class="ml-2 text-sm text-gray-700">Enable Recording</span>
                            </label>
                            
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="motion_detection" value="1" {{ old('motion_detection', $cctv->motion_detection) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-5 h-5">
                                <span class="ml-2 text-sm text-gray-700">Motion Detection</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="border-t border-gray-200 pt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">{{ old('notes', $cctv->notes) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Additional notes about this camera</p>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-4 border-t border-gray-200">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('cctv.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-xl shadow-md transition-all transform hover:scale-105">
                            Update Camera
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // IP address validation
    const ipInput = document.querySelector('input[name="camera_ip"]');
    if (ipInput) {
        ipInput.addEventListener('blur', function() {
            const ipPattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            if (this.value && !ipPattern.test(this.value)) {
                alert('Please enter a valid IP address');
                this.focus();
            }
        });
    }
</script>
@endpush
@endsection