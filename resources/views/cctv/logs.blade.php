@extends('layouts.app')

@section('title', 'CCTV Logs')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">CCTV Event Logs</h2>
                    <p class="text-amber-100 text-sm mt-1">Security event history and recordings</p>
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
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Camera</label>
                    <select name="cctv_id" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Cameras</option>
                        @foreach($cameras as $camera)
                        <option value="{{ $camera->id }}" {{ request('cctv_id') == $camera->id ? 'selected' : '' }}>
                            {{ $camera->camera_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                    <select name="event_type" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Events</option>
                        <option value="motion" {{ request('event_type') == 'motion' ? 'selected' : '' }}>Motion Detected</option>
                        <option value="intrusion" {{ request('event_type') == 'intrusion' ? 'selected' : '' }}>Intrusion</option>
                        <option value="alert" {{ request('event_type') == 'alert' ? 'selected' : '' }}>Security Alert</option>
                        <option value="recording" {{ request('event_type') == 'recording' ? 'selected' : '' }}>Recording Started</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Logs Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-amber-50 to-orange-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Camera</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Event Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Location</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $log->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $log->cctv->camera_name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ $log->cctv->camera_location ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $log->event_type == 'motion' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($log->event_type == 'intrusion' ? 'bg-red-100 text-red-800' : 
                                       ($log->event_type == 'alert' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ ucfirst($log->event_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $log->event_data['description'] ?? 'No additional details' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $log->cctv->camera_location ?? 'Unknown' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">No security events found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection