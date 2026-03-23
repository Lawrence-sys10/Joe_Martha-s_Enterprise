@extends('layouts.app')

@section('title', 'Camera Management')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Camera Management</h2>
                    <p class="text-amber-100 text-sm mt-1">Configure and manage CCTV cameras</p>
                </div>
                <a href="{{ route('cctv.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Add Camera Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Add New Camera</h3>
                <p class="text-sm text-gray-500 mt-1">Configure your CCTV camera connection</p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('cctv.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Camera Name *</label>
                            <input type="text" name="camera_name" required class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Camera IP Address *</label>
                            <input type="text" name="camera_ip" placeholder="192.168.1.100" required class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Camera Location</label>
                            <input type="text" name="camera_location" placeholder="Main Entrance, Back Door, etc." required class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Camera Type</label>
                            <select name="camera_type" required class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                                <option value="rtsp">RTSP (IP Cameras)</option>
                                <option value="http">HTTP/HTTPS (Webcam/IP)</option>
                                <option value="onvif">ONVIF Compatible</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                            <input type="number" name="port" placeholder="554" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                            <p class="text-xs text-gray-500 mt-1">Default: 554 for RTSP, 80 for HTTP</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stream URL (Optional)</label>
                            <input type="text" name="stream_url" placeholder="rtsp://192.168.1.100:554/stream" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                            <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" name="username" placeholder="admin" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="recording_enabled" value="1" checked class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                            <span class="ml-2 text-sm text-gray-700">Enable Recording</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="motion_detection" value="1" checked class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                            <span class="ml-2 text-sm text-gray-700">Motion Detection</span>
                        </label>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-all">
                            Add Camera
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Camera List -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Configured Cameras</h3>
                <p class="text-sm text-gray-500 mt-1">Manage your existing cameras</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Camera</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Recording</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cameras as $camera)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $camera->camera_name }}</div>
                                <div class="text-xs text-gray-500">{{ $camera->camera_location }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $camera->camera_ip }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 uppercase">{{ $camera->camera_type }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1">
                                    <div class="w-2 h-2 {{ $camera->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></div>
                                    <span class="text-xs {{ $camera->is_active ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $camera->is_active ? 'Online' : 'Offline' }}
                                    </span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs {{ $camera->recording_enabled ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $camera->recording_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <button onclick="testConnection({{ $camera->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Test</button>
                                <button onclick="editCamera({{ $camera->id }})" class="text-amber-600 hover:text-amber-900 mr-3">Edit</button>
                                <form action="{{ route('cctv.destroy', $camera) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this camera?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $cameras->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function testConnection(cameraId) {
    fetch(`/cctv/${cameraId}/test`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                alert('Camera is online and reachable!');
            } else {
                alert('Camera is offline or unreachable. Check connection settings.');
            }
        })
        .catch(error => {
            alert('Error testing connection: ' + error);
        });
}
</script>
@endsection