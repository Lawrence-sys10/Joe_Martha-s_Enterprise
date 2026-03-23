@extends('layouts.app')

@section('title', 'CCTV Monitoring')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">CCTV Monitoring System</h2>
                    <p class="text-amber-100 text-sm mt-1">Real-time surveillance and security monitoring</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 bg-green-500/20 backdrop-blur-sm rounded-lg px-3 py-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-white text-sm">All Cameras Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Camera Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($cameras as $camera)
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
                                <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 aspect-video">
                    @if($camera->stream_url)
                        <img src="{{ $camera->stream_url }}" alt="{{ $camera->camera_name }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">No Stream Available</p>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Camera Status Badge -->
                    <div class="absolute top-2 left-2 flex items-center gap-2">
                        <div class="w-2 h-2 {{ $camera->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full animate-pulse"></div>
                        <span class="text-xs text-white bg-black/50 px-2 py-1 rounded-full">{{ $camera->is_active ? 'Live' : 'Offline' }}</span>
                    </div>
                    
                    <!-- Recording Indicator -->
                    @if($camera->recording_enabled)
                    <div class="absolute top-2 right-2">
                        <div class="flex items-center gap-1 bg-red-500/80 px-2 py-1 rounded-full">
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            <span class="text-xs text-white">REC</span>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $camera->camera_name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $camera->camera_location }}</p>
                        </div>
                        @if($camera->motion_detection)
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Motion Detection</span>
                        @endif
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">IP Address:</span>
                            <span class="text-gray-700 font-mono">{{ $camera->camera_ip ?? 'Not Configured' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Recording:</span>
                            <span class="{{ $camera->recording_enabled ? 'text-green-600' : 'text-red-600' }}">
                                {{ $camera->recording_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route("cctv.edit", $camera) }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-medium transition-colors text-center">
                            Edit
                        </a>
                        @if($camera->stream_url)
                        <button onclick="viewStream({{ $camera->id }})" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                            View Live
                        </button>
                        @endif
                        <button onclick="viewLogs({{ $camera->id }})" class="flex-1 border-2 border-amber-500 text-amber-600 hover:bg-amber-50 py-2 rounded-lg text-sm font-medium transition-colors">
                            View Logs
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Recent Activity Log -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Recent Security Events
                </h3>
            </div>
            <div class="p-6">
                @if($recentLogs->count() > 0)
                <div class="space-y-3">
                    @foreach($recentLogs as $log)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $log->event_type }}</p>
                                <p class="text-sm text-gray-500">Camera: {{ $log->cctv->camera_name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">{{ $log->created_at->diffForHumans() }}</p>
                            <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500">No security events recorded</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function viewStream(cameraId) {
        window.open(`/cctv/stream/${cameraId}`, '_blank', 'width=800,height=600');
    }
    
    function viewLogs(cameraId) {
        window.location.href = `/cctv/logs?cctv_id=${cameraId}`;
    }
</script>
@endpush
@endsection